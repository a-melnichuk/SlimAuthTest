<div id="movie-container">
    <div id ="movie-img">
        <img src="<?= __SITE_LINK ?>src/movie_icon.png">
    </div>
    <div id="movie-info">
        <h2><?= $title ?></h2>
        <h3>Info:</h3>
        <p>id: <?= $id ?></p>
        <p>year: <?= $year ?></p>
        <p>format: <?= $format ?></p>
        <p>actors: <?= $actors ?></p>
        <form action="<?= route('movies/remove') ?>" method="post">
                <button name="remove" value="<?= $id ?>">Delete</button>
        </form>
    </div>
</div>
