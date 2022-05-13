<?php declare(strict_types=1);

use Mailery\Activity\Log\Widget\ActivityLogLink;
use Mailery\Icon\Icon;
use Mailery\Widget\Link\Link;
use Mailery\Web\Widget\DateTimeFormat;
use Yiisoft\Yii\Bootstrap5\Nav;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Activity\Log\Entity\Event $event */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle('Activity log #' . $event->getId());

?><div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        <h4 class="mb-0">Activity log #<?= $event->getId(); ?></h4>
                    </div>
                    <div class="col-auto">
                        <div class="btn-toolbar float-right">
                            <a class="btn btn-sm btn-outline-secondary mx-sm-1" href="<?= $url->generate('/activity-log/default/index'); ?>">
                                Back
                            </a>
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
                <?= Nav::widget()
                    ->currentPath($currentRoute->getUri()->getPath())
                    ->items([
                        [
                            'label' => 'Overview',
                            'url' => $url->generate($event->getViewRouteName(), $event->getViewRouteParams()),
                        ],
                    ])
                    ->options([
                        'class' => 'nav nav-tabs nav-tabs-bordered font-weight-bold',
                    ])
                    ->withoutEncodeLabels();
                ?>

                <div class="mb-4"></div>
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
