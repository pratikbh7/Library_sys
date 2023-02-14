<?php if(!defined('PATH')){
    require_once '/var/www/html/components/youshallnotpass.php';
}?>
<div class="table-wrapper">
    <table class="fl-table">
        <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Release Year</th>
            <th>Status</th>
            <th>Overtime</th>
        </tr>
        </thead>
        <tbody>
        <?php 
        global $data;
        foreach($data as $key => $value){
                    $back = "blue" . '_cell';
                    $overtime = "";
                    if( $data['status'] === 1 ){
                        $back = "red" . '_cell';
                        $burrowed = filter_var($data['Burrowed Date'], FILTER_SANITIZE_STRING );
                        $burrowed = strtotime($burrowed);
                        $now = strtotime("now");
                        $difference = $now - $burrowed;
                        $penalty = 0;
                        if( $difference > 86400){ //10 days
                            $penalty = 10 * ( intval($difference/ 8640) );
                            $overtime = htmlspecialchars("past due");
                        }
                        $back = $back . '_cell';
                        $back = htmlspecialchars($back);
                    }
                ?>  
                <tr>
                    <td><?php echo htmlspecialchars(filter_var($value['Title'], FILTER_SANITIZE_STRING)); ?></td>
                    <td><?php echo htmlspecialchars(filter_var($value['Author'], FILTER_SANITIZE_STRING)); ?></td>
                    <td><?php echo htmlspecialchars(filter_var($value['Release Year'], FILTER_SANITIZE_STRING)); ?></td>
                    <td class="<?php echo $back; ?>"></td>
                    <td data-penalty = <?php echo $penalty; ?>><?php echo $overtime; ?></td>
                </tr>
            <?php } ?>
        <tbody>
    </table>
</div>