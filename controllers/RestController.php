<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
//use yii\filters\auth\CompositeAuth;
//use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
//use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use app\models\Rest;
use yii\web\BadRequestHttpException;

class RestController extends Controller
{
  public function beforeAction($action)
  { 
    //set JSON for respobse output
    Yii::$app->response->format = Response::FORMAT_JSON;

    //set checking for valid content-type, only 'application/json' accepted
    if (Yii::$app->request->headers->get('content-type') != 'application/json')
      throw new BadRequestHttpException('Not valid content type');

    //if route is not getcats, we need some payload
    if (empty(Yii::$app->request->bodyParams) && !in_array($action->id,['getcats','getproducts']))
      throw new BadRequestHttpException('Payload can not be empty');

    return parent::beforeAction($action);

  }

  /**
   * Summary of behaviors
   * @return array
   */
  public function behaviors()
  {
    return [
      ['class' => HttpBearerAuth::class],
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          'getcats' => ['GET'],
          'addcats' => ['POST'],
          'updatecat' => ['PUT'],
          'deletecat' => ['DELETE'],
          'getproducts' => ['GET'],
          'addproduct' => ['POST'],
          'updateproduct' => ['PUT'],
          'deleteproduct' => ['DELETE'],
        ],
      ],
    ];
  }

  /**
   * Summary of actionGetcats
   * @return array
   */
  public function actionGetcats()
  {
    return Rest::getCategories();
  }

  /**
   * Summary of actionAddcat
   * @return array
   */
  public function actionAddcat()
  {
    return Rest::addCategories();
  }

  /**
   * Summary of actionUpdatecat
   * @return array
   */
  public function actionUpdatecat()
  {
    return Rest::updateCategory();
  }

  /**
   * Summary of actionDeletecat
   * @return array
   */
  public function actionDeletecat()
  {
    return Rest::deleteCategory();
  }

  /**
   * Summary of actionGetproducts
   * @return array
   */
  public function actionGetproducts()
  {
    return Rest::getProducts();
  }

  /**
   * Summary of actionAddproducts
   * @return array
   */
  public function actionAddproduct()
  {
    return Rest::addProduct();
  }

  /**
   * Summary of actionUpdateproduct
   * @return array
   */
  public function actionUpdateproduct()
  {
    return Rest::updateProduct();
  }

  /**
   * Summary of actionDeleteproduct
   * @return array
   */
  public function actionDeleteproduct()
  {
    return Rest::deleteProduct();
  }

}
