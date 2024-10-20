
# ACF Dynamic Shortcode

## Overview
This WordPress shortcode allows you to dynamically retrieve Advanced Custom Fields (ACF) field values with additional flexibility, such as accessing subfields, properties, or repeater field indices.

### Requirements
- **ACF Plugin**: The Advanced Custom Fields plugin must be installed and active for this shortcode to function.
- **WordPress**: This shortcode works with WordPress installations using ACF fields.

## Installation

1. Copy the provided `acf_field` shortcode function to your theme's `functions.php` file or include it in a custom plugin.
2. Ensure that ACF is installed and active on your site.

## Usage

### Basic Field Retrieval
To retrieve the value of a simple field:

```shortcode
[acf_field field="logo"]
```

This will fetch the `logo` field from the current post.

### Repeater Field with Index
To get a specific repeater item’s field value by index:

```shortcode
[acf_field group="team" field="position" index="0"]
```

This will return the 'position' field from the first item in the `team` repeater field.

### Image URL
To get the URL of an image field:

```shortcode
[acf_field group="gallery" field="image" return="url"]
```

This returns the URL of the `image` field within the `gallery` group.

### Special Output Handler
For custom field group outputs, such as link groups, use:

```shortcode
[acf_field group="links" field="github" output="odkazy_group_links"]
```

This outputs a set of links from the specified field group.

## Shortcode Attributes

| Attribute    | Description                                                                                             | Example                                   |
|--------------|---------------------------------------------------------------------------------------------------------|-------------------------------------------|
| `group`      | ACF group name (optional).                                                                               | `group="team"`                            |
| `field`      | ACF field name (required unless `output` is specified).                                                  | `field="logo"`                            |
| `subfield`   | Subfield within a repeater or group field (optional).                                                    | `subfield="url"`                          |
| `index`      | Index for repeater fields (optional).                                                                    | `index="0"`                               |
| `post_id`    | Post ID to retrieve the field from (optional, defaults to current post).                                  | `post_id="123"`                           |
| `allow_html` | Whether to allow HTML in the output (default: false).                                                    | `allow_html="true"`                       |
| `return`     | Return format: `value`, `id`, `url`, or `array`.                                                         | `return="url"`                            |
| `get_label`  | Return the field label instead of the value (default: false).                                             | `get_label="true"`                        |
| `fallback`   | Fallback value if the field is empty or not found.                                                       | `fallback="N/A"`                          |
| `output`     | Special output handling (e.g., `odkazy_group_links`).                                                    | `output="odkazy_group_links"`             |

## Examples

- Retrieve a simple ACF field value:
  ```shortcode
  [acf_field field="logo"]
  ```

- Retrieve a value from a repeater field at index 0:
  ```shortcode
  [acf_field group="team" field="position" index="0"]
  ```

- Return an image URL:
  ```shortcode
  [acf_field group="gallery" field="image" return="url"]
  ```

## License
This code is open source and available under the MIT License.
