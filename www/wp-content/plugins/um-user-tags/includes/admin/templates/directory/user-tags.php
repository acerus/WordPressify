<div class="um-admin-metabox">

    <?php $tags = get_option( 'um_user_tags_filters' );

    if ( $tags ) {

        $fields = array(
            array(
                'id'		=> '_um_user_tags_on',
                'type'		=> 'checkbox',
                'name'		=> '_um_user_tags_on',
                'label'		=> __( 'Show only users with current tags', 'um-user-tags' ),
                'value'		=> UM()->query()->get_meta_value( '_um_user_tags_on' ),
            ),
        );

        foreach( $tags as $tag => $term_id ) {
            $data = UM()->fields()->get_field( $tag );

            $terms_list = get_terms( 'um_user_tag', array(
                'hide_empty' => 0,
                'child_of'   => $data['tag_source']
            ) );

            $options = array();
            foreach ( $terms_list as $term ) {
                $options[$term->term_id] = $term->name;
            }

            $post_id = get_the_ID();
            $user_tags = get_post_meta( $post_id, '_um_user_tags_' . $tag, true );

            $fields[] = array(
                'id'		=> '_um_user_tags_' . $tag,
                'type'		=> 'select',
                'name'		=> '_um_user_tags_' . $tag,
                'label'		=> $data['title'],
                'options'	=> $options,
                'multi'		=> true,
                'value'		=> $user_tags,
                'conditional' => array( '_um_user_tags_on', '=', '1' )
            ); ?>
        <?php }

        UM()->admin_forms( array(
            'class'		=> 'um-member-directory-user-tags um-half-column',
            'prefix_id'	=> 'um_metadata',
            'fields'    => $fields
        ) )->render_form();

    } else {
        _e( 'You did not create any user tags fields yet.', 'um-user-tags' );
    } ?>

    <div class="um-admin-clear"></div>
</div>