
/**
 * Shortcode to dynamically retrieve ACF field values with enhanced functionality.
 *
 * Attributes:
 * - group (string): Name of the ACF field group (optional).
 * - field (string): Name of the ACF field (required unless 'output' is set).
 * - subfield (string): Name of a subfield within a repeater or group field (optional).
 * - property (string): Specific property of the field (optional).
 * - index (int): Index for repeater fields (optional).
 * - allow_html (bool): Whether to allow HTML output (default: false).
 * - post_id (int): The ID of the post to retrieve the field from (optional, defaults to current post).
 * - return (string): Return format: 'value' (default), 'id', 'url', 'array' (optional).
 * - get_label (bool): Return the field label instead of the value (default: false).
 * - fallback (string): Fallback value if the field is empty or not found (optional).
 * - output (string): Special output handler for custom field groups (e.g., 'odkazy_group_links') (optional).
 *
 * Example Usage:
 * [acf_field field="logo"]
 * Retrieves the value of the 'logo' field for the current post.
 *
 * [acf_field group="team" field="position" index="0"]
 * Retrieves the 'position' field from the first repeater item in the 'team' group.
 *
 * [acf_field group="gallery" field="image" return="url"]
 * Retrieves the URL of an image field from the 'gallery' group.
 *
 * [acf_field group="links" field="github" output="odkazy_group_links"]
 * Outputs links for the 'odkazy_group_links' custom handler.
 *
 * @param array $atts Shortcode attributes.
 * @return string The field value or fallback.
 */
function get_acf_field($atts) {
    $atts = shortcode_atts(
        array(
            'group'          => '',        // Group field name (optional)
            'field'          => '',        // Field name (required unless 'output' is set)
            'subfield'       => '',        // Subfield name (optional)
            'property'       => '',        // Property of the field (optional)
            'index'          => '',        // Index for repeater fields (optional)
            'allow_html'     => false,     // Allow HTML output (default: false)
            'post_id'        => '',        // Post ID to get the field from (optional)
            'return'         => 'value',   // Return format: 'value' (default), 'id', 'url', 'array'
            'get_label'      => false,     // Return the field label instead of value (default: false)
            'fallback'       => '',        // Fallback value if field is empty or not found
            'output'         => '',        // Special output handling (e.g., 'odkazy_group_links')
        ),
        $atts,
        'acf_field'
    );

    // Convert 'get_label' and 'allow_html' to boolean
    $get_label = filter_var( $atts['get_label'], FILTER_VALIDATE_BOOLEAN );
    $allow_html = filter_var( $atts['allow_html'], FILTER_VALIDATE_BOOLEAN );

    // Check if ACF is installed
    if ( ! function_exists( 'get_field' ) ) {
        return $atts['fallback']; // Return fallback if ACF is not active
    }

    // Determine the post ID
    $post_id = $atts['post_id'] ? intval( $atts['post_id'] ) : get_the_ID();

    // Handle special outputs
    if ( $atts['output'] === 'odkazy_group_links' ) {
        // Retrieve the 'odkazy_group' field
        $odkazy_group = get_field( 'odkazy_group', $post_id );

        if ( $odkazy_group && is_array( $odkazy_group ) ) {
            $output = '<ul>';

            // Fields to process within the group
            $fields = array( 'github', 'softwarovy_web', 'softwarovy_web_kopirovat' );

            foreach ( $fields as $field_name ) {
                // Get the field value
                $field_value = isset( $odkazy_group[ $field_name ] ) ? $odkazy_group[ $field_name ] : '';

                // Check if the field has a value
                if ( ! empty( $field_value ) ) {
                    // Get the field object to retrieve the label
                    $field_object = get_field_object( "odkazy_group_$field_name", $post_id );

                    // Use the field label or generate one if not available
                    $label = isset( $field_object['label'] ) ? $field_object['label'] : ucfirst( str_replace( '_', ' ', $field_name ) );

                    // Append the link to the output
                    $output .= '<li><a href="' . esc_url( $field_value ) . '">' . esc_html( $label ) . '</a></li>';
                } else {
                    // Fallback handling when the field value is empty
                    // You can choose to skip displaying the link or provide default values

                    // Option 1: Skip displaying the link
                    // continue;

                    // Option 2: Display a link with default URL and label
                    $fallback_url = '#'; // Default URL if none provided
                    $fallback_label = ucfirst( str_replace( '_', ' ', $field_name ) ); // Default label

                    $output .= '<li><a href="' . esc_url( $fallback_url ) . '">' . esc_html( $fallback_label ) . '</a></li>';
                }
            }

            // Handle the 'jine' group field
            if ( isset( $odkazy_group['jine'] ) && is_array( $odkazy_group['jine'] ) ) {
                $jine = $odkazy_group['jine'];

                // Get the 'nazev' and 'url' subfields
                $nazev = isset( $jine['nazev'] ) && ! empty( $jine['nazev'] ) ? $jine['nazev'] : 'Default Name';
                $url   = isset( $jine['url'] ) && ! empty( $jine['url'] ) ? $jine['url'] : '#';

                // Append the 'jine' link to the output
                $output .= '<li><a href="' . esc_url( $url ) . '">' . esc_html( $nazev ) . '</a></li>';
            } else {
                // Fallback for 'jine' field when it's not set or empty
                // Optionally, you can choose to skip this or provide default values

                // Option 1: Skip displaying the 'jine' link
                // Do nothing

                // Option 2: Display a link with default values
                $output .= '<li><a href="#">Default Name</a></li>';
            }

            $output .= '</ul>';

            return $output;
        } else {
            // Fallback when 'odkazy_group' is not set or is not an array
            return $atts['fallback'];
        }
    }

    // Ensure 'field' attribute is provided
    if ( empty( $atts['field'] ) ) {
        return $atts['fallback']; // Return fallback if 'field' is not provided
    }

    // Build the field path
    $field_path = array();

    // Add group to the path if provided
    if ( ! empty( $atts['group'] ) ) {
        $field_path[] = $atts['group'];
    }

    // Add main field
    $field_path[] = $atts['field'];

    // Add index if provided
    if ( is_numeric( $atts['index'] ) ) {
        $field_path[] = intval( $atts['index'] );
    }

    // Add subfield if provided
    if ( ! empty( $atts['subfield'] ) ) {
        $field_path[] = $atts['subfield'];
    }

    // Build the field name path for get_field_object
    $field_name_path = implode( '_', array_filter( $field_path ) );

    // Start fetching the field value or object
    if ( $get_label ) {
        // Get the field object
        $field_object = get_field_object( $field_name_path, $post_id );
        if ( $field_object && isset( $field_object['label'] ) ) {
            $field_value = $field_object['label'];
        } else {
            return $atts['fallback'];
        }
    } else {
        // Get the field value
        $field_name  = array_shift( $field_path );
        $field_value = get_field( $field_name, $post_id );

        // Traverse the field path
        foreach ( $field_path as $key ) {
            if ( is_array( $field_value ) && array_key_exists( $key, $field_value ) ) {
                $field_value = $field_value[ $key ];
            } else {
                // Field not found, return fallback
                return $atts['fallback'];
            }
        }

        // Handle image fields
        if ( is_array( $field_value ) && isset( $field_value['ID'] ) ) {
            if ( $atts['return'] === 'id' ) {
                $field_value = $field_value['ID'];
            } elseif ( $atts['return'] === 'url' ) {
                $field_value = $field_value['url'];
            } elseif ( $atts['return'] === 'array' ) {
                // Return the array as a JSON string
                $field_value = wp_json_encode( $field_value );
            } else {
                // Default to returning the image URL
                $field_value = $field_value['url'];
            }
        }

        // Access property if specified
        elseif ( ! empty( $atts['property'] ) && is_array( $field_value ) ) {
            if ( isset( $field_value[ $atts['property'] ] ) ) {
                $field_value = $field_value[ $atts['property'] ];
            } else {
                // Property not found, return fallback
                return $atts['fallback'];
            }
        }
    }

    // Check if the field value is empty
    if ( empty( $field_value ) ) {
        return $atts['fallback'];
    }

    // Output the value, allowing HTML if specified
    if ( $allow_html ) {
        return wp_kses_post( $field_value );
    } else {
        return esc_html( $field_value );
    }
}
add_shortcode( 'acf_field', 'get_acf_field' );
