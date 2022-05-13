<?php declare(strict_types=1);

use Mailery\Activity\Log\Entity\Event;
use Mailery\Web\Widget\DateTimeFormat;
use Mailery\Web\Widget\EntityViewLink;
use Mailery\Brand\Exception\BrandRequiredException;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\Widgets\ContentDecorator;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Activity\Log\Entity\Event $event */
/** @var Mailery\Activity\Log\Provider\EntityGroupsProvider $entityGroups */

$this->setTitle('Activity log #' . $event->getId());

?>

<?= ContentDecorator::widget()
    ->viewFile('@vendor/maileryio/mailery-activity-log/views/default/_layout.php')
    ->parameters(compact('event', 'csrf'))
    ->begin(); ?>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <h6 class="font-weight-bold">General details</h6>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= DetailView::widget()
            ->model($event)
            ->options([
                'class' => 'table detail-view',
            ])
            ->emptyValue('<span class="text-muted">(not set)</span>')
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
        <h6 class="font-weight-bold">Data changes</h6>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
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

<?= ContentDecorator::end() ?>
