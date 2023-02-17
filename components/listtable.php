<?php if(!defined('PATH')){
    require_once '/var/www/html/components/youshallnotpass.php';
}?>
<div class="action_status"><p></p></div>
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
        $index = 0;
        foreach($data as $key => $value){
                    $back = "blue" . '_cell';
                    $overtime = "";
                    $penalty = 0;
                    $book_action = htmlspecialchars("Issue");
                    if( $value['Status'] === 1 ){
                        $back = "red";
                        $book_action = htmlspecialchars("Return");
                        $burrowed = filter_var($value['Burrowed Date'], FILTER_SANITIZE_STRING );
                        $burrowed = strtotime($burrowed);
                        $now = strtotime("now");
                        $difference = $now - $burrowed;
                        if( $difference > 864000){ //10 days
                            $penalty = 10 * ( intval($difference/ 864000) );
                            $overtime = htmlspecialchars("past due");
                        }
                        $back = $back . '_cell';
                        $back = htmlspecialchars($back);
                    }
                ?>
                <div class="book_action" id="action_<?php echo $index;?>">
                    <a class="boxclose" id="boxclose"></a>
                    <ul>
                        <li><a href="javascript:void(0)" class="the_action"><?php echo $book_action ?></a></li>
                        <li><a href="javascript:void(0)" class="delete_book">Delete</a></li>
                    </ul>
                </div>  
                <tr id="identifier_<?php echo $index;?>" class="book_row">
                    <td><a href="javascript:void(0)" class="book_desc" id="book_id_<?php echo $index;?>" ><?php echo htmlspecialchars(filter_var($value['Title'], FILTER_SANITIZE_STRING)); ?></a></td>
                    <td><?php echo htmlspecialchars(filter_var($value['Author'], FILTER_SANITIZE_STRING)); ?></td>
                    <td><?php echo htmlspecialchars(filter_var($value['Release Year'], FILTER_SANITIZE_STRING)); ?></td>
                    <td class="<?php echo $back; ?>"></td>
                    <td data-penalty = <?php echo $penalty; ?>><?php echo $overtime; ?></td>
                </tr>
            <?php 
            ++$index;
        } ?>
        <tbody>
    </table>
</div>
<form id="action_form" method="POST">
    <div class="input-group" id="burrower">
        <label for="burrower">Burrower:</label>
        <input required type="text" name="action_input[burrower]" id="burrower" autocomplete="off" placeholder="burrower"/> 
    </div>
    <button type="submit" id ="action_submit">ISSUE</button>
</form>