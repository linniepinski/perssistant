<script type="text/template" id="message-item">
    <div class="message-item row" chat_id="{{= id }}">
        <div class="hidden-xs hidden-sm col-md-1 the_author_chat">
            {{html avatar }}
        </div>
        <div class="col-xs-8 col-sm-9 col-md-9 the_content_chat">
            <p class="aut">{{= display_name }}</p>
            <p class="cont">
                {{html content }}
            </p>
            {{html link }}
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 the_time_chat">
        <span class="time" data-toggle="tooltip" data-placement="top"
              title="{{= date1 }}">{{= date2 }}</span>
        </div>
    </div>
</script>
