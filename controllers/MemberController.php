<?php

namespace app\controllers;

use Yii;
use app\models\Member;
use app\models\Contact;
use app\models\search\MemberSearch;
use app\components\controllers\BaseController;

use app\models\forms\UploadForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\components\ExcelParser;

/**
 * MemberController implements the CRUD actions for Member model.
 */
class MemberController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Member models.
     * @return mixed
     */
    public function actionIndex($poll_id)
    {
        $searchModel = new MemberSearch();
        $params = Yii::$app->request->queryParams;
        $params[$searchModel->formName()]['poll_id'] = $poll_id;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'poll_id'=>$poll_id,
        ]);
    }

    /**
     * Displays a single Member model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Member model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($poll_id)
    {
        $model = new Member();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /* Parses an Excel file and creates multiple instances of the Member model.
        * If creation is successful, the browser will be redirected to the
        * 'index' page. */
    public function actionImport($poll_id)
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->excelFile = UploadedFile::getInstance($model, 'excelFile');
            $file = $model->upload();
            if ($file) {
                $member_dicts = ExcelParser::parseMembers($file->tempName);
                $transaction = Yii::$app->db->beginTransaction();
                foreach ($member_dicts as $dict) {
                    $member = new Member();
                    $member->poll_id = $poll_id;
                    $member->name = $dict['name'];
                    $member->group = $dict['group'];
                    if($member->save()) {
                        foreach($dict['contacts'] as $contact_dict) {
                            $contact = new Contact();
                            $contact->member_id = $member->id;
                            $contact->name = $contact_dict['name'];
                            $contact->email = $contact_dict['email'];
                            if(!$contact->save()) {
                                Yii::trace('Contact failed to save');
                                var_dump($contact);
                                die();
                                $transaction->rollback();
                                return $this->render('import', [
                                    'model' => $model,
                                ]);
                            }
                        }
                    } else {
                        Yii::trace('Member failed to save');
                        $transaction->rollback();
                        return $this->render('import', [
                            'model' => $model,
                        ]);
                    }
                }
                $transaction->commit();
                return $this->redirect('index');
            }
        }
        return $this->render('import', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Member model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Member model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($poll_id, $id)
    {
        //$this->findModel($id)->delete();
        $member = $this->findModel($id);

        $transaction = Yii::$app->db->beginTransaction();
        if($member->delete()) {
            $transaction->commit();
        } else {
            $transaction->rollback();
            Yii::$app->getSession()->setFlash('error', 'Could not delete member');
        }
        return $this->redirect(['index', 'poll_id' => $poll_id]);
    }


    /**
     * Finds the Member model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Member the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Member::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}