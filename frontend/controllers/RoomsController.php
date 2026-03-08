<?php

namespace frontend\controllers;

use app\models\Rooms;
use app\models\roomsForm;
use app\models\roomProducts;
use app\models\Bets;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
use Yii;

/**
 * RoomsController implements the CRUD actions for Rooms model.
 */
class RoomsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Rooms models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new roomsForm();

        if(Yii::$app->user->identity->username == "admin"){
            $dataProvider = $searchModel->search($this->request->queryParams);
        }else{
            $approved_rooms = Rooms::getRoomsOfApprovedUser(Yii::$app->user->id);
            if(!empty($approved_rooms)){
                $dataProvider = $searchModel->search($this->request->queryParams,'',$approved_rooms);
            }else{
                $dataProvider = $searchModel->search($this->request->queryParams,'','null');
            }
;        }
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            
        ]);
    }

    /**
     * Displays a single Rooms model.
     * @param int $room_id Room ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($room_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($room_id),
        ]);
    }

    /**
     * Creates a new Rooms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Rooms();
        if (!$model->status) {
            $model->status = 'active'; // default value
        }
        $product_query = new Query();
        $product_list = $product_query->select(['product_id', 'product_name'])
            ->from('products')
            ->all();
        $user_list = User::find()
            ->select(['id', 'username'])   // only the columns you need
            ->where(['!=', 'username', 'admin'])
            ->asArray()                    // return plain arrays
            ->all();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                
                $postData = $this->request->post(); // <- define $postData here

                
                $model->created_at = date('Y-m-d H:i:s');
                $model->updated_at = date('Y-m-d H:i:s');
                
                try {
                    if ($model->save()) {

                        // Save RoomProducts
                        if (!empty($postData['product_ids'])) {
                            foreach ($postData['product_ids'] as $product_id) {
                                $roomProduct = new RoomProducts();
                                $roomProduct->room_id_fk = $model->room_id;
                                $roomProduct->product_id_fk = $product_id;
                                if (!$roomProduct->save()) {
                                    throw new \Exception("Failed to save RoomProduct: " . json_encode($roomProduct->errors));
                                }
                            }
                        }

                        // Save Bets
                        foreach ($postData['user_ids'] as $user_id) {
                            foreach ($postData['product_ids'] as $product_id) {
                                $bet = new Bets();
                                $bet->room_id_fk = $model->room_id;
                                $bet->product_id_fk = $product_id;
                                $bet->user_id_fk = $user_id;
                                $bet->bet_amount = 0;
                                $bet->bet_time = 0;
                                
                                if (!$bet->save()) {
                                    throw new \Exception("Failed to save Bet: " . json_encode($bet->errors));
                                }
                            }
                        }


                        return $this->redirect(['index.php']);
                    }
                }catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    return $this->redirect(['rooms']);
                }  
            } else {
                $model->loadDefaultValues();
            }

            
        }
        return $this->render('create', [
            'model' => $model,
            'product_list' => $product_list,
            'user_list' => $user_list,
        ]);
    }

    /**
     * Updates an existing Rooms model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $room_id Room ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($room_id)
    {
        $model = $this->findModel($room_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'room_id' => $model->room_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Rooms model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $room_id Room ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($room_id)
    {
        $this->findModel($room_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Displays the selected bidding Rooms model.
     * @param int $room_id Room ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBid($room_id)
    {
        //get product list for the respective room
        if($room_id){
            $request = Yii::$app->request;
            

            $product_query = new Query();
            $product_list = $product_query->select(['product_id_fk', 'products.*'])
                ->from('room_products')
                ->leftJoin('products', 'room_products.product_id_fk = products.product_id')
                ->where(['room_id_fk' => $room_id])
                ->all();

            if ($request->isPost){

                $bet_model = new Bets();
                // Handle POST data
                $data = $request->post();

                //update bets table
                foreach($product_list as $product){
                    $product_id_bet = $product['product_id']."_bet_amt"; // setup the key to find bet amt
                    Yii::$app->db->createCommand()->update(
                        'bets',
                        [
                            'bet_amount' => $data[$product_id_bet],
                            'bet_time' => $dateTimeNow = date('Y-m-d H:i:s')
                        ],
                        [
                            'room_id_fk' => $room_id,
                            'product_id_fk' => $product['product_id'],
                            'user_id_fk' => $data['user_id']
                        ]
                    )->execute();
                }
                
                // Redirect somewhere
                return $this->render('bid_summary', [
                    'model' => $this->findModel($room_id),
                    'product_list' => $product_list,
                    'bet_list' => $bet_model->getRoomBetDetails($room_id),
                    'approved_users' => $bet_model->getApprovedUsersOfRoom($room_id),
                    'data' => $data
                ]);
            }
            

            return $this->render('bid', [
                'model' => $this->findModel($room_id),
                'product_list' => $product_list,
                'room_id' => $room_id,
                'approved_users' => Rooms::getApprovedUsersOfRoom($room_id),
            ]);

        }else{
            throw new \yii\web\NotFoundHttpException('Room is not being selected.');
        }
    }

    /**
     * Displays the selected bidding summary Rooms model.
     * @param int $room_id Room ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBid_summary($room_id)
    {
        //get product list for the respective room
        if($room_id){

            $product_query = new Query();
            $product_list = $product_query->select(['product_id_fk', 'products.*'])
                ->from('room_products')
                ->leftJoin('products', 'room_products.product_id_fk = products.product_id')
                ->where(['room_id_fk' => $room_id])
                ->all();

            Bets::checkUpdateBetsEntry($room_id);
            /*return $this->render('bid_summary', [
                'model' => $this->findModel($room_id),
                'product_list' => $product_list,
                'bet_list' => Bets::getRoomBetDetails($room_id),
                'room_id' => $room_id,
            ]);*/

            //get approved player list
            $summary_display_array = [];
            $player_list = Rooms::getApprovedUsersOfRoom($room_id);
            foreach ($player_list as $player){
                $player_array = [];
                $player_details = User::findIdentity($player);
                $player_array['id'] = $player_details['id'];
                $player_array['username'] = $player_details['username'];
                $product_count = 0;
                $final_total = 0;
                $final_text = "";
                foreach($product_list as $product){
                    $product_count++;
                    $product_key_id = "product_".$product_count."_id";
                    $player_array[$product_key_id] = $product['product_id'];
                    $bet_details = Bets::findOne(['room_id_fk' => $room_id, 'product_id_fk' => $product['product_id'], 'user_id_fk' => $player_details['id']]);
                    $product_id_amt = "product_".$product_count."_amt";
                    $player_array[$product_id_amt] = $bet_details['bet_amount'];
                    $product_id_amt_projected = "product_".$product_count."_amt_projected";
                    
                    if($product['product_type'] == "Riskless"){
                        $player_array[$product_id_amt_projected] = $bet_details['bet_amount'];
                        $final_total = $final_total + $bet_details['bet_amount'];
                        $final_text = $final_text."$".$bet_details['bet_amount']." (".$product['product_name'].")";
                    }
                    if($product['product_type'] == "Pooled Investment"){
                        $pooled_detail = Bets::getPooledInvestmentDetails($room_id, $product['product_id']);
                        $player_array[$product_id_amt_projected] = $pooled_detail['pooled_amt_individual'];
                        $final_total = $final_total + $pooled_detail['pooled_amt_individual'];
                        $final_text = $final_text." + $".$pooled_detail['pooled_amt_individual']." (".$product['product_name'].")";
                    }
                }
                $player_array['final'] = $final_text." = $".$final_total;
                $summary_display_array[] = $player_array;
            }

            return $this->render('bid_summary', [
                'model' => $this->findModel($room_id),
                'product_list' => $product_list,
                'summary_display_array' => $summary_display_array,
                "player_list" => $player_list,
                'room_id' => $room_id,
            ]);


        }else{
            throw new \yii\web\NotFoundHttpException('Room is not being selected.');
        }
    }

    /**
     * Finds the Rooms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $room_id Room ID
     * @return Rooms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($room_id)
    {
        if (($model = Rooms::findOne(['room_id' => $room_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
