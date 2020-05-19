<?php declare(strict_types=1);

use Mailery\Activity\Log\Entity\Event;
use Mailery\Widget\Dataview\DetailView;

/** @var Mailery\Web\View\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Activity\Log\Entity\Event $event */
$this->setTitle('Activity log #' . $event->getId());

?><div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3">
            <h1 class="h2">Activity log #<?= $event->getId(); ?></h1>
            <div class="btn-toolbar float-right">
                <div class="btn-toolbar float-right">
                    <a class="btn btn-sm btn-outline-secondary mx-sm-1 mb-2" href="<?= $urlGenerator->generate('/activity-log/default/index'); ?>">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12 grid-margin">
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
                    'label' => 'Id',
                    'value' => function (Event $data, $index) {
                        return $data->getId();
                    },
                ],
                [
                    'label' => 'Date',
                    'value' => function (Event $data, $index) {
                        return $data->getDate()->format('Y-m-d H:i:s');
                    },
                ],
            ]);
        ?>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <h2 class="h2">Data changes</h2>

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
                    echo '<tr>'
                            . '<td>' . $dataChanges->getField() . '</td>'
                            . '<td>' . $dataChanges->getValueOld() . '</td>'
                            . '<td>' . $dataChanges->getValueNew() . '</td>'
                        . '</tr>';
                } ?>
            </tbody>
        </table>
    </div>
</div>
