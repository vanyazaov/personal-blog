         <!-- ARTICLE -->
        <article class="single-post">

            <!-- HERO IMAGE -->
            <div class="single-post__image">
                <img
                    src="/images/<?= e($post->picture) ?>"
                    alt="Post cover image"
                >
            </div>

            <!-- HEADER -->
            <header class="single-post__header">

                <!-- CATEGORIES -->
                <div class="single-post__categories">

                    <a href="#" class="post-category">
                        Design
                    </a>

                    <a href="#" class="post-category">
                        Lifestyle
                    </a>

                </div>

                <!-- TITLE -->
                <h1 class="single-post__title">
                    <?= e($post->title) ?>
                </h1>

                <!-- DESCRIPTION -->
                <p class="single-post__description">
                    Short article description. This text is usually used
                    as preview content for SEO and article cards.
                </p>

                <!-- META -->
                <div class="single-post__meta">

                    <span class="post-meta-item">
                        <?= fmt_date($post->created_at) ?>
                    </span>

                    <span class="post-meta-separator">
                        •
                    </span>

                    <span class="post-meta-item">
                        2451 views
                    </span>

                </div>

            </header>

            <!-- CONTENT -->
            <div class="single-post__content">

                <?= e($post->body) ?>

            </div>

        </article>

        <!-- RELATED POSTS -->
        <section class="related-posts">

            <div class="section-header">

                <h2 class="section-title">
                    Similar articles
                </h2>

            </div>

            <div class="posts-grid">

                <?php foreach($relatedPosts as $relPost): ?>
                <!-- CARD -->
                <article class="post-card">

                    <a href="/category/<?= e($categoryId) ?>/<?= e($relPost->id) ?>" class="post-image-link">
                        <img
                            src="/images/<?= e($relPost->picture) ?>"
                            alt=""
                            class="post-image"
                        >
                    </a>

                    <div class="post-content">

                        <h3 class="post-title">
                            <a href="/category/<?= e($categoryId) ?>/<?= e($relPost->id) ?>">
                                <?= e($relPost->title) ?>
                            </a>
                        </h3>

                        <time class="post-date">
                            <?= fmt_date($relPost->created_at) ?>
                        </time>

                        <p class="post-excerpt">
                            <?= truncate(e($relPost->body)) ?>
                        </p>

                        <a href="/category/<?= e($categoryId) ?>/<?= e($relPost->id) ?>" class="read-more">
                            Continue Reading
                        </a>

                    </div>

                </article>
                <?php endforeach; ?>

            </div>

        </section>