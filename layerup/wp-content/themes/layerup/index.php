<?php get_header(); ?>

<div id="categories">
    <h2>Categorias</h2>
    <ul>
        <?php
        $categories = get_categories();
        $image_index = 1; // Índice inicial para as imagens
        
        foreach ($categories as $category) {
            // Obtendo a imagem correspondente
            $image_path = get_template_directory_uri() . '/images/' . str_pad($image_index, 2, '0', STR_PAD_LEFT) . '.jpg';
            echo '<li>';
            echo '<a href="#" class="category-link" data-id="' . $category->term_id . '">';
            echo '<img src="' . $image_path . '" alt="' . $category->name . '" style="width:100px;height:auto;" />'; // Imagem da categoria
            echo $category->name;
            echo '</a>';
            echo '</li>';
            $image_index++;
        }
        ?>
    </ul>
</div>

<div id="posts">
    <h2>Posts</h2>
    <ul id="post-list">
        <?php
        $posts = get_posts();
        foreach ($posts as $post) {
            setup_postdata($post);
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        wp_reset_postdata();
        ?>
    </ul>
</div>

<?php get_footer(); ?>
