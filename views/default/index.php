<?php declare(strict_types=1);

use Mailery\Activity\Log\Entity\Event;
use Mailery\Web\Widget\DateTimeFormat;
use Mailery\Web\Widget\EntityViewLink;
use Mailery\Brand\Exception\BrandRequiredException;
use Yiisoft\Yii\DataView\GridView;

/** @var Yiisoft\Yii\WebView $this */
/** @var Yiisoft\Aliases\Aliases $aliases */
/** @var Yiisoft\Router\UrlGeneratorInterface $url */
/** @var Yiisoft\Data\Paginator\PaginatorInterface $paginator */
/** @var Mailery\Activity\Log\Provider\EntityGroupsProvider $entityGroups */

$this->setTitle('Activity log');

?><div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        <h4 class="mb-0">Activity log</h4>
                    </div>
                    <div class="col-auto">
                        <div class="btn-toolbar float-right">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <?= GridView::widget()
                    ->layout("{items}\n<div class=\"mb-4\"></div>\n{summary}\n<div class=\"float-right\">{pager}</div>")
                    ->options([
                        'class' => 'table-responsive',
                    ])
                    ->tableOptions([
                        'class' => 'table table-hover',
                    ])
                    ->emptyText('No data')
                    ->emptyTextOptions([
                        'class' => 'text-center text-muted mt-4 mb-4',
                    ])
                    ->paginator($paginator)
                    ->currentPage($paginator->getCurrentPage())
                    ->columns([
                        [
                            'label()' => ['Date'],
                            'value()' => [fn (Event $model) => EntityViewLink::widget()
                                ->entity($model)
                                ->label(
                                    DateTimeFormat::widget()
                                        ->dateTime($model->getDate())
                                        ->run()
                                )
                            ],
                        ],
                        [
                            'label()' => ['User'],
                            'value()' => [static function (Event $model) {
                                if (($user = $model->getUser()) === null) {
                                    return null;
                                }

                                return EntityViewLink::widget()
                                    ->entity($user)
                                    ->label($user->getUsername());
                            }],
                        ],
                        [
                            'label()' => ['Group'],
                            'value()' => [fn (Event $model) => $entityGroups->getGroupByKey($model->getGroup())->getLabel()],
                        ],
                        [
                            'label()' => ['Action'],
                            'value()' => [fn (Event $model) => $model->getAction()],
                        ],
                        [
                            'label()' => ['Object'],
                            'value()' => [static function (Event $model) {
                                if (($className = $model->getObjectClass()) !== null && class_exists($className)) {
                                    $entity = new $className;

                                    if ($model->getObjectId() && method_exists($entity, 'setId')) {
                                        $entity->setId($model->getObjectId());
                                    }

                                    try {
                                        return EntityViewLink::widget()
                                            ->entity($entity)
                                            ->reload(true)
                                            ->label($model->getObjectLabel())
                                            ->routeParams(array_filter([
                                                'brandId' => $model->getBrand()?->getId(),
                                            ]))
                                            ->render();
                                    } catch (BrandRequiredException $e) {}
                                }

                                return $model->getObjectLabel();
                            }],
                        ],
                    ]);
                ?>
            </div>
        </div>
    </div>
</div>
