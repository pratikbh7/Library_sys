<?php global $data; ?>
<div id="book_count">
    <ul id="count_list">
        <li>ISSUED :<?php echo(htmlspecialchars(filter_var($data['Total_count']))) ?></li>
        <li>TOTAL : <?php echo(htmlspecialchars(filter_var($data['issue_count']))) ?></li>
    </ul>
</div>