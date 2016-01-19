<?php

?>
<div class="modal modal-vcenter fade" id="popup_invate_freelancer_to_chat">
    <div class="modal-dialog top-margin">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true" style="font-size: 28px;">&times;</span></button>
                <h4 class="modal-title text-center text-color-popup">Invite freelancer</h4>
            </div>
            <hr>
            <form id="invate_freelancer_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">


                            <label>Invitation message</label>
                            <textarea id="invate_message_id" name="invate_message"
                                      class="form-control invate-to-chat-message">

                            </textarea>

                            <input type="hidden" id="project_id_invate" name="project_id_invate" value="">
                            <input type="hidden" id="sender_id" name="sender_id" value="">
                            <input type="hidden" id="reciever_id" name="reciever_id" value="">
                            <input type="hidden" id="guid" name="guid" value="">
                            <input type="hidden" id="title" name="title" value="">


                            <button type="button" class="btn btn-info pull-right btn-invate-cancel" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary pull-right btn-invate">Send invitation</button>
                        </div>
                    </div>
                </div>

            </form>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div><!-- /.modal -->