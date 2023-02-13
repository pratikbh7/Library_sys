<p class="ajax-status"></p>
<form id="add-book" method="POST" >
    <div id="input-group">
        <label for="book_title">TITLE</label>
        <input required type="text" name="add_book[title]" id="book_title" autocomplete="off" placeholder="title">
    </div>
    <div id="input-group">
        <label for="book_author">AUTHOR</label>
        <input required type="text" name="add_book[author]" id="book_author" autocomplete="off" placeholder="author">
    </div>
    <div id="input-group">
        <label for="book_year">RELEASE YEAR</label>
        <input required type="text" name="add_book[rel_year]" id="book_year" autocomplete="off" placeholder="rel_year">
    </div>
    <button type="submit" id="add-btn">ADD BOOK</button>
</form> 