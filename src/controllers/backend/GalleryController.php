<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Gallery\controllers\backend;

use Besnovatyj\Gallery\forms\backend\gallery\GalleryForm;
use Besnovatyj\Gallery\forms\backend\search\GallerySearch;
use Besnovatyj\Gallery\image\GalleryImageOwner;
use Besnovatyj\Gallery\repositories\GalleryRepository;
use Besnovatyj\Gallery\services\manage\GalleryManageService;
use Besnovatyj\Images\helpers\ImageActionsMap;
use Besnovatyj\Gallery\entities\gallery\Image;
use common\components\controller\ControllerTrait;
use common\components\urlmanager\UrlManagerHelperTrait;
use DomainException;
use Exception;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\Response;

class GalleryController extends Controller
{
    use ControllerTrait;
    use UrlManagerHelperTrait;

    private GalleryManageService $service;
    private GalleryRepository $galleryRepo;

    public function __construct(
        $id,
        $module,
        GalleryManageService $service,
        GalleryRepository $galleryRepo,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
        $this->service     = $service;
        $this->galleryRepo = $galleryRepo;
    }

    /**
     * Регистрирует standalone image-actions через ImageActionsMap.
     *
     * Gallery передаёт GalleryImageOwner, который реализует pessimistic lock
     * (SELECT FOR UPDATE) для исключения race condition при параллельной загрузке.
     *
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return ImageActionsMap::get(
            Image::class,
            fn(int $id) => new GalleryImageOwner($this->galleryRepo->get($id), $this->galleryRepo),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'activate'       => ['POST'],
                    'draft'          => ['POST'],
                    'delete'         => ['POST'],
                    'add-image'      => ['POST'],
                    'delete-image'   => ['POST'],
                    'set-main-image' => ['POST'],
                    'get-images'     => ['POST'],
                    'set-new-sort'   => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel  = new GallerySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return Response|string
     * @throws InvalidConfigException
     */
    public function actionView(int $id): Response|string
    {
        try {
            $absoluteFrontendUrl = $this->getAbsoluteFrontendRoute('/Gallery/gallery/gallery/', ['id' => $id]);
            return $this->render('view', [
                'gallery'             => $this->galleryRepo->get($id),
                'absoluteFrontendUrl' => $absoluteFrontendUrl,
            ]);
        } catch (DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        return $this->goHome();
    }

    /**
     * @return Response|string
     */
    public function actionCreate(): Response|string
    {
        $form = new GalleryForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $gallery = $this->service->create($form);
                return $this->redirect(['view', 'id' => $gallery->id]);
            } catch (Throwable $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * @param int $id
     * @return Response|string
     */
    public function actionUpdate(int $id): Response|string
    {
        $gallery = $this->galleryRepo->get($id);
        $form    = new GalleryForm($gallery);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->edit($gallery->id, $form);
                return $this->redirect(['view', 'id' => $gallery->id]);
            } catch (Throwable $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }

        return $this->render('update', [
            'model'   => $form,
            'gallery' => $gallery,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionDelete(int $id): Response
    {
        try {
            $this->service->remove($id);
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->redirect(['index']);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionActivate(int $id): Response
    {
        try {
            $this->service->activate($id);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->goReferer();
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionDraft(int $id): Response
    {
        try {
            $this->service->draft($id);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->goReferer();
    }

}
