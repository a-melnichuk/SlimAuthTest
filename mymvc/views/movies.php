<?php if($message !== '') echo "<h3>$message<h3>"; ?>
<div>
    <button id="btn-add-movie">
        <a href="<?= route('movies/add') ?>">Add movie</a>
    </button>
</div>
<div> <!-- only reason for post here is...prettiness of url... -->
    <form type="post" action = "<?= __SITE_LINK . 'movies/search' ?>">
        Search by
        <select name="type">
          <option value="title" <?php if($type === 'title') echo 'selected="selected"'; ?>>title</option>
          <option value="name"  <?php if($type === 'name') echo 'selected="selected"';?> >name</option>
        </select>
        <input pattern=".{1,}"  required type="text" name="val" <?php if($val !== '') echo 'value="' . $val .'"'; ?> >
        <button type="submit" value="Submit">Search</button>
    </form>
</div>
<div id = "movies">
    <?php if($movies === -1): ?>
        <h3 id="err-msg">No records have been found. :(</h3>
    <?php else: ?>
        <?php foreach($movies as $movie): ?>
        <div class="movie">
            <a href="<?= route('movies/movie') ?>&id=<?= $movie['id'] ?>"><img src="<?= __SITE_LINK ?>src/movie_icon.png" width="150"></a>
            <p><?= $movie['title']; ?></p>
            <div class="btn-container">
                <button>
                    <a href="<?= route('movies/movie') ?>&id=<?= $movie['id'] ?>">More Info</a>
                </button>
                <form action="<?= route('movies/remove') ?>" method="post">
                    <button name="remove" value="<?= $movie['id'] ?>">Delete</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
