<script type="text/template" id="ae-profile-loop">
    <div class="avatar-proflie">
        <a href="{{= permalink }}" >{{= et_avatar }}</a>   
    </div>
    <div class="user-proflie">
        <a href="{{= permalink }}" class="name">{{= author_name }}</a>
        <span class="position">{{= et_professional_title }}</span>
    </div>
    <div class="clearfix"></div>
    <ul class="wrapper-achivement">
        <li>
            <div class="rate-it" data-score="{{= rating_score }}"></div>
        </li>
        <li><span>{{= hourly_rate_price }}</span></li>
        <li><span>{{= experience }}</span></li>
    </ul>
</script>