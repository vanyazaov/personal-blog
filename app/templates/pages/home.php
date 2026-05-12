            <?php foreach($categoryPost as $categoryId => $data): ?>
            <!-- CATEGORY -->
            <section class="category-section">

                <div class="section-header">
                    <h2 class="section-title"><?= e($data['category_name']) ?></h2>

                    <a href="/category/<?= e($categoryId) ?>" class="view-all">
                        View All
                    </a>
                </div>

                <div class="posts-grid">
                    <?php foreach($data['posts'] as $post): ?>
                    <!-- POST -->
                    <article class="post-card">

                        <a href="/category/<?= e($categoryId) ?>/<?= e($post->id) ?>" class="post-image-link">
                            <img
                                src="/images/<?= e($post->picture) ?>"
                                alt="Post image"
                                class="post-image"
                            >
                        </a>

                        <div class="post-content">

                            <h3 class="post-title">
                                <a href="/category/<?= e($categoryId) ?>/<?= e($post->id) ?>">
                                    <?= e($post->title) ?>
                                </a>
                            </h3>

                            <time class="post-date" datetime="<?= fmt_date($post->created_at) ?>">
                                <?= fmt_date($post->created_at) ?>
                            </time>

                            <p class="post-excerpt">
                                <?=  truncate(e($post->body)) ?>
                            </p>

                            <a href="/category/<?= e($categoryId) ?>/<?= e($post->id) ?>" class="read-more">
                                Continue Reading
                            </a>

                        </div>

                    </article>
                    <?php endforeach; ?>
                </div>

            </section>
            <?php endforeach; ?>