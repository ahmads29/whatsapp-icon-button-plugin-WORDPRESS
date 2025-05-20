<?php
/**
 * Plugin Name: WhatsApp Chat Plugin By Ahmad Sharkawi
 * Description: A simple plugin to add WhatsApp chat support with dynamic numbers and profiles.
 * Version: 1.9
 * Author: Your Name
 */

// Enqueue scripts and styles
function whatsapp_chat_enqueue_scripts() {
    wp_enqueue_style('whatsapp-chat-style', plugin_dir_url(__FILE__) . 'css/style.css');
    wp_enqueue_script('whatsapp-chat-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'whatsapp_chat_enqueue_scripts');

// Create admin menu
function whatsapp_chat_create_menu() {
    add_menu_page('WhatsApp Chat Settings', 'WhatsApp Chat', 'administrator', 'whatsapp-chat-settings', 'whatsapp_chat_settings_page', 'dashicons-whatsapp', 110);
    add_action('admin_init', 'register_whatsapp_chat_settings');
}
add_action('admin_menu', 'whatsapp_chat_create_menu');

// Register settings
function register_whatsapp_chat_settings() {
    register_setting('whatsapp-chat-settings-group', 'whatsapp_chat_data');
}

// Admin page HTML
function whatsapp_chat_settings_page() {
    $whatsapp_data = get_option('whatsapp_chat_data', array('contacts' => array()));
    ?>
    <div class="wrap">
        <h1>WhatsApp Chat Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('whatsapp-chat-settings-group'); ?>
            <?php do_settings_sections('whatsapp-chat-settings-group'); ?>

            <h2>Add WhatsApp Contacts</h2>

            <div id="whatsapp-contacts">
                <?php foreach ($whatsapp_data['contacts'] as $index => $contact): ?>
                    <div class="whatsapp-contact-row">
                        <input type="text" name="whatsapp_chat_data[contacts][<?php echo $index; ?>][name]" value="<?php echo esc_attr($contact['name']); ?>" placeholder="Name" required />
                        <input type="text" name="whatsapp_chat_data[contacts][<?php echo $index; ?>][department]" value="<?php echo esc_attr($contact['department']); ?>" placeholder="Department" required />
                        <input type="text" name="whatsapp_chat_data[contacts][<?php echo $index; ?>][number]" value="<?php echo esc_attr($contact['number']); ?>" placeholder="WhatsApp Number (without +)" required />
                        <button type="button" class="remove-contact">Remove</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" id="add-contact" class="button button-secondary">Add Another Contact</button>
            <?php submit_button(); ?>
        </form>
    </div>

    <script>
        document.getElementById('add-contact').addEventListener('click', function() {
            const container = document.getElementById('whatsapp-contacts');
            const newContact = document.createElement('div');
            newContact.className = 'whatsapp-contact-row';
            newContact.innerHTML = `
                <input type="text" name="whatsapp_chat_data[contacts][${container.children.length}][name]" placeholder="Name" required />
                <input type="text" name="whatsapp_chat_data[contacts][${container.children.length}][department]" placeholder="Department" required />
                <input type="text" name="whatsapp_chat_data[contacts][${container.children.length}][number]" placeholder="WhatsApp Number (without +)" required />
                <button type="button" class="remove-contact">Remove</button>
            `;
            container.appendChild(newContact);
        });

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-contact')) {
                event.target.parentElement.remove();
            }
        });
    </script>
    <?php
}

// Frontend display
function whatsapp_chat_display() {
    $whatsapp_data = get_option('whatsapp_chat_data', array('contacts' => array()));

    if (!empty($whatsapp_data['contacts'])) {
        echo '<div class="whatsapp-chat-button" id="whatsapp-chat-button">';
        echo '<img src="' . plugin_dir_url(__FILE__) . 'assets/whatsapp-icon.png" alt="WhatsApp" class="main-whatsapp-icon">';
        echo '</div>';
        echo '<div class="whatsapp-chat-popup" id="whatsapp-chat-popup">';
        echo '<h4>Need support? Contact us on:</h4>';
        
        foreach ($whatsapp_data['contacts'] as $contact) {
            echo '<div class="whatsapp-contact">';
            echo '<img src="' . plugin_dir_url(__FILE__) . 'assets/whatsapp-icon.png" class="contact-icon" alt="WhatsApp">';
            echo '<a href="https://wa.me/' . esc_attr($contact['number']) . '" target="_blank" class="contact-link">';
            echo '<span class="contact-name">' . esc_html($contact['name']) . '</span>';
            echo '<br>';
            echo '<span class="contact-department">' . esc_html($contact['department']) . '</span>';
            echo '</a>';
            echo '</div>';
        }

        echo '</div>';
    }
}
add_action('wp_footer', 'whatsapp_chat_display');
