<?php declare(strict_types=1);

use Mailery\Activity\Log\Entity\Event;
use Mailery\Widget\Dataview\DetailView;
use Mailery\Web\Widget\DateTimeFormat;
use Mailery\Web\Widget\EntityViewLink;
use Mailery\Brand\Exception\BrandRequiredException;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Activity\Log\Entity\Event $event */
/** @var Mailery\Activity\Log\Provider\EntityGroupsProvider $entityGroups */

$this->setTitle('Activity log #' . $event->getId());

?><div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3">
            <h1 class="h3">Activity log #<?= $event->getId(); ?></h1>
            <div class="btn-toolbar float-right">
                <div class="btn-toolbar float-right">
                    <a class="btn btn-sm btn-outline-secondary mx-sm-1 mb-2" href="<?= $url->generate('/activity-log/default/index'); ?>">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= DetailView::widget()
            ->data($event)
            ->options([
                'class' => 'table detail-view',
            ])
            ->emptyText('(not set)')
            ->emptyTextOptions([
                'class' => 'text-muted',
            ])
            ->attributes([
                [
                    'label' => 'Date',
                    'value' => function (Event $data, $index) {
                        return DateTimeFormat::widget()->dateTime($data->getDate());
                    },
                ],
                [
                    'label' => 'User',
                    'value' => function (Event $data, $index) {
                        if (($user = $data->getUser()) === null) {
                            return null;
                        }

                        return EntityViewLink::widget()
                            ->entity($user)
                            ->label($user->getUsername());
                    },
                ],
                [
                    'label' => 'Group',
                    'value' => function (Event $data, $index) use($entityGroups) {
                        return $entityGroups->getGroupByKey($data->getGroup())->getLabel();
                    },
                ],
                [
                    'label' => 'Action',
                    'value' => function (Event $data, $index) {
                        return $data->getAction();
                    },
                ],
                [
                    'label' => 'Object',
                    'value' => function (Event $data, $index) {
                        if (($entity = $data->getEntity()) !== null) {
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
                    },
                ],
                [
                    'label' => 'Object Id',
                    'value' => function (Event $data, $index) {
                        if ($data->getObjectClass() && $data->getObjectId()) {
                            return $data->getObjectClass() . '#' . $data->getObjectId();
                        }
                        return null;
                    },
                ],
            ]);
        ?>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <h2 class="h4">Data changes</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Old value</th>
                    <th>New value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($event->getObjectDataChanges() as $dataChanges) {
                    $valueMask = null;
                    if (($entity = $event->getEntity()) !== null && in_array($dataChanges->getField(), $entity->getMaskedFields())) {
                        $valueMask = '******';
                    }

                    echo '<tr>'
                            . '<td>' . $dataChanges->getField() . '</td>'
                            . '<td>' . ($valueMask ?? $dataChanges->getValueOld()) . '</td>'
                            . '<td>' . ($valueMask ?? $dataChanges->getValueNew()) . '</td>'
                        . '</tr>';
                } ?>
            </tbody>
        </table>
    </div>
</div>
