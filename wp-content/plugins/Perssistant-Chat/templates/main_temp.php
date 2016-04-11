<?php
$current_user = wp_get_current_user();
?>

<form ID="chat" enctype="multipart/form-data">
    <!--    <div class="row height-inherit">-->
    <div id="contacts" class="col-xs-12 col-sm-4 col-md-3 col-lg-3 height-inherit">
        <section class="sieve sieve-custom">

        </section>
    </div>

    <div id="right-column-chat" class="col-xs-12 col-sm-8 col-md-9 col-lg-9 right-column-chat">
        <div class="main-container">

            <div id="contact">

            </div>

            <hr class="hr-chat">
            <a id="loadprev"
               class="btn btn-default btn-sm btn-block"><?php _e("Load previous messages", 'chat-frontend') ?></a>
            <hr class="hr-chat">

            <div id="chat_his" class="chat_history">
                <div class="panell">
                    <h3 style="text-align: center"><?php _e("Select a contact to start a chat", 'chat-frontend') ?></h3>
                </div>
            </div>


            <hr>
        </div>


        <div class="control-row">
                <textarea name="name_edit_message" id="edit_message" class="form-control message_box_chat"
                          rows="3"></textarea>
            <br>

            <div class="row">
                <div id="status-chat" class="col-xs-12 col-sm-12 col-md-6">
                    <div class="alert alert-status" id="status-alert">
                        <button type="button" class="close"></button>
                        <strong></strong>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-6">
                    <input type="hidden" name="contact_with" id="contact_with" class="contact_with"
                           value="<?php echo $chat_contact; ?>">
                    <input id="send" class="btn btn-view-profile send_button_chat" type="submit"
                           value="<?php _e("Send message", 'chat-frontend') ?>">


                    <?php
                    wp_nonce_field(plugin_basename(__FILE__), 'wp_custom_attachment_nonce');


                    $html = '<input type="file" id="wp_custom_attachment" data-filename-placement="inside" name="wp_custom_attachment" class="btn btn-primary pull-right" value="" size="25" title="' . __("Attach file", 'chat-frontend') . '" />';

                    echo $html;

                    ?>
                </div>
            </div>
        </div>
    </div>
    <!--    </div>-->
</form>
