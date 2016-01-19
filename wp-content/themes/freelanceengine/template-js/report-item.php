<script type="text/template" id="ae-report-loop">
    <div class="form-group-work-place">
        <div class="info-avatar-report">
            <a href="#" class="avatar-employer-report">
               {{=avatar}}
            </a>
            <div class="info-report">
                <span class="name-report">{{= display_name }}</span>
                <div class="date-chat-report">
                    {{= message_time }}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="content-report-wrapper">
            <div class="content-report">
                {{= comment_content }}
            </div>
            <# if(file_list){ #>
            <div class="title-attachment"><?php _e("Attachment", ET_DOMAIN); ?></div>
            {{= file_list }}
            <# } #>
        </div>
    </div>
</script>
