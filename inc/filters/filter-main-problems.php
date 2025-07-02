<div class="filter-item has-submenu">
    <a href="#" class="filter-link">
        <svg class="stroke-style" xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
            <path d="M12.0008 9.39986V13.3999M12.0008 17.3999H12.0108M10.2908 4.25986L1.82075 18.3999C1.64612 18.7023 1.55372 19.0452 1.55274 19.3944C1.55176 19.7436 1.64224 20.087 1.81518 20.3904C1.98812 20.6938 2.23748 20.9466 2.53846 21.1237C2.83944 21.3008 3.18155 21.396 3.53075 21.3999H20.4708C20.82 21.396 21.1621 21.3008 21.463 21.1237C21.764 20.9466 22.0134 20.6938 22.1863 20.3904C22.3593 20.087 22.4497 19.7436 22.4488 19.3944C22.4478 19.0452 22.3554 18.7023 22.1808 18.3999L13.7108 4.25986C13.5325 3.96597 13.2815 3.72298 12.9819 3.55435C12.6824 3.38571 12.3445 3.29712 12.0008 3.29712C11.657 3.29712 11.3191 3.38571 11.0196 3.55435C10.72 3.72298 10.469 3.96597 10.2908 4.25986Z" stroke="#757575" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="filter-title">
            Main
            <br> 
            Problems
        </span>
    </a>
    <div class="submenu">
        <span class="filter-title-mobile">Main Problems</span>
        <?php
        $terms = get_terms([
            'taxonomy' => 'main-problems',
            'hide_empty' => false,
        ]);

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                echo '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
            }
        } else {
            echo '<p>No problems found.</p>';
        }
        ?>
    </div>      
</div>