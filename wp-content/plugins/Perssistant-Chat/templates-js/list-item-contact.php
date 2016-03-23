<script type="text/template" id="list-contact-item">
    <div class="item_contact" id_contact="{{= id}}">
        <div class="avatar">{{html avatar}}</div>
        <div class="author">{{= display_name}}</div>
        <div class="status">
            {{if status_offline}}
            <span class="status-online {{= status_offline}}"></span>
            {{/if}}
            {{if status_afk}}
            <span class="status-online {{= status_afk}}"></span>
            {{/if}}
            {{if status_online}}
            <span class="status-online {{= status_online}}"></span>
            {{/if}}
        </div>
        <div class="count"><span class="badge">{{= count}}</span></div>
    </div>
</script>
