<?php declare(strict_types=1);

use Mailery\Activity\Log\Entity\Event;
use Mailery\Widget\Dataview\Columns\DataColumn;
use Mailery\Widget\Dataview\GridView;
use Mailery\Widget\Dataview\GridView\LinkPager;
use Mailery\Web\Widget\EntityViewLink;
use Mailery\Brand\Exception\BrandRequiredException;

/** @var Yiisoft\Yii\WebView $this */
/** @var Yiisoft\Aliases\Aliases $aliases */
/** @var Yiisoft\Router\UrlGeneratorInterface $urlGenerator */
/** @var Yiisoft\Data\Reader\DataReaderInterface $dataReader*/
/** @var Yiisoft\Data\Paginator\PaginatorInterface $paginator */
$this->setTitle('Activity log');

?><div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3">
            <h1 class="h3">Activity log</h1>
            <div class="btn-toolbar float-right">
            </div>
        </div>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= GridView::widget()
            ->paginator($paginator)
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
            ->columns([
                (new DataColumn())
                    ->header('Date')
                    ->content(function (Event $data, int $index) {
                        return EntityViewLink::widget()
                            ->entity($data)
                            ->label($data->getDate()->format('Y-m-d H:i:s'));
                    }),
                (new DataColumn())
                    ->header('User')
                    ->content(function (Event $data, int $index) {
                        if (($user = $data->getUser()) === null) {
                            return null;
                        }

                        return EntityViewLink::widget()
                            ->entity($user)
                            ->label($user->getUsername());
                    }),
                (new DataColumn())
                    ->header('Module')
                    ->content(function (Event $data, int $index) {
                        return $data->getModule();
                    }),
                (new DataColumn())
                    ->header('Action')
                    ->content(function (Event $data, int $index) {
                        return $data->getAction();
                    }),
                (new DataColumn())
                    ->header('Object')
                    ->content(function (Event $data, int $index) {
                        if (($className = $data->getObjectClass()) !== null && class_exists($className)) {
                            $entity = new $className;

                            if ($data->getObjectId() && method_exists($entity, 'setId')) {
                                $entity->setId($data->getObjectId());
                            }

                            try {
                                return EntityViewLink::widget()
                                    ->entity($entity)
                                    ->reload(true)
                                    ->label($data->getObjectLabel())
                                    ->routeParams(array_filter([
                                        'brandId' => $data->getBrand()?->getId(),
                                    ]))
                                    ->render();
                            } catch (BrandRequiredException $e) {}
                        }

                        return $data->getObjectLabel();
                    }),
            ]);
        ?>
    </div>
</div><?php
if ($paginator->getTotalItems() > 0) {
            ?><div class="mb-4"></div>
    <div class="row">
        <div class="col-6">
            <?= GridView\OffsetSummary::widget()
                ->paginator($paginator); ?>
        </div>
        <div class="col-6">
            <?= LinkPager::widget()
                ->paginator($paginator)
                ->options([
                    'class' => 'float-right',
                ])
                ->prevPageLabel('Previous')
                ->nextPageLabel('Next')
                ->urlGenerator(function (int $page) use ($urlGenerator) {
                    $url = $urlGenerator->generate('/activity-log/default/index');
                    if ($page > 1) {
                        $url = $url . '?page=' . $page;
                    }

                    return $url;
                }); ?>
        </div>
    </div><?php
        }
?>
