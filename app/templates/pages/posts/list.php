            <!-- CATEGORY -->
            <section class="category-section">

                <div class="section-header">
                    <h2 class="section-title">Category <?= $data['categoryId'] ?></h2>

                    <a href="#" class="view-all">
                        View All
                    </a>
                </div>

                <div class="posts-grid">

                    <!-- POST -->
                     <?php foreach($posts as $post): ?>
                    <article class="post-card">

                        <a href="#" class="post-image-link">
                            <img
                                src="/images/post-1.png"
                                alt="Post image"
                                class="post-image"
                            >
                        </a>

                        <div class="post-content">

                            <h3 class="post-title">
                                <a href="#">
                                    <?=  htmlspecialchars($post['title'], ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8') ?>
                                </a>
                            </h3>

                            <time class="post-date" datetime="2019-07-19">
                                May 6, 2026
                            </time>

                            <p class="post-excerpt">
                                <?=  htmlspecialchars($post['body'], ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8') ?>
                            </p>

                            <a href="#" class="read-more">
                                Continue Reading
                            </a>

                        </div>

                    </article>
                    <?php endforeach; ?>              

                </div>

            </section>

