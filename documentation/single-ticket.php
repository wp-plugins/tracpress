<?php get_header(); ?>

<section id="content" role="main" class="ip-main">
    <?php if(have_posts()) : while(have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php tp_main(get_the_ID()); ?>
        </article>

        <?php comments_template(); ?>
    <?php endwhile; endif; ?>
</section>

<?php get_footer(); ?>
