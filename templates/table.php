<div class="table-container">
    <table class="stripped nowrap hover table">
        <thead>
            <tr>
                <th style="width:1px"><input type="checkbox"></th>
                <?php echo $self->renderColumns() ?>
                <?php echo $self->renderAction() ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($self->isEmpty($entries)): ?>
                <tr><td style="text-align: center" colspan="100"><?php ll('No row available') ?></td></tr>
            <?php else: ?>
                <?php foreach ($entries as $key => $entry): ?>
                <tr>
                    <td><input type="checkbox"></td>
                    <?php echo $self->renderColumns($entry) ?>
                    <?php echo $self->renderAction($entry) ?>
                </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>
</div>