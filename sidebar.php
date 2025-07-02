<?php

$enable_sidebar = get_field('enable_sidebar', 'option'); // 'option' is required for Options Page

if ($enable_sidebar) : ?>
    <div class="filter-section">
    <!-- Search Filter -->
    <div class="first-section">
        <div class="search-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                <path d="M19.6 21.3999L13.3 15.0999C12.8 15.4999 12.225 15.8166 11.575 16.0499C10.925 16.2832 10.2333 16.3999 9.5 16.3999C7.68333 16.3999 6.14583 15.7707 4.8875 14.5124C3.62917 13.2541 3 11.7166 3 9.8999C3 8.08324 3.62917 6.54574 4.8875 5.2874C6.14583 4.02907 7.68333 3.3999 9.5 3.3999C11.3167 3.3999 12.8542 4.02907 14.1125 5.2874C15.3708 6.54574 16 8.08324 16 9.8999C16 10.6332 15.8833 11.3249 15.65 11.9749C15.4167 12.6249 15.1 13.1999 14.7 13.6999L21 19.9999L19.6 21.3999ZM9.5 14.3999C10.75 14.3999 11.8125 13.9624 12.6875 13.0874C13.5625 12.2124 14 11.1499 14 9.8999C14 8.6499 13.5625 7.5874 12.6875 6.7124C11.8125 5.8374 10.75 5.3999 9.5 5.3999C8.25 5.3999 7.1875 5.8374 6.3125 6.7124C5.4375 7.5874 5 8.6499 5 9.8999C5 11.1499 5.4375 12.2124 6.3125 13.0874C7.1875 13.9624 8.25 14.3999 9.5 14.3999Z" fill="white"/>
            </svg>
        </div>
        <div class="filter-item">
            <span>
                Filter
                <br>
                By
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none">
                 <path d="M2 8.3999L7 13.3999L12 8.3999M2 1.3999L7 6.3999L12 1.3999" stroke="#1E1E1E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>

    <!-- Main Filters (e.g., Categories, Region, etc.) -->
    <div class="filter-list">
        <?php 
            get_template_part('inc/filters/filter', 'main-problems'); 
            get_template_part('inc/filters/filter', 'categories');
            get_template_part('inc/filters/filter', 'region');
            get_template_part('inc/filters/filter', 'taget-group');
            get_template_part('inc/filters/filter', 'shared-learning');
            get_template_part('inc/filters/filter', 'by-ngo');
            get_template_part('inc/filters/filter', 'behind-scene');
        ?>
    </div>
</div>

 <?php endif;