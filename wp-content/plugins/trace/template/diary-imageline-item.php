<div class="col-xs-2 no-padding-lr <?php if ($query->image_url == 'deleted') {
                        echo 'deleted-item';
                    } else {
                        echo 'data-block';
                    } ?>" id="item-<?php echo $query->id; ?>">
                        <?php

                        if ($query->image_url != 'deleted') {
                            ?>
                            <img src="<?php echo $query->image_url ?>" class="img-responsive img-confirm">
                            <div class="row info-block">
                                <div class="col-xs-6 no-padding-r">
                                    <input type="checkbox" class="time-item" id="<?php echo $query->id; ?>"
                                           value="<?php echo $query->id; ?>" data-parent="<?php echo $row; ?>"
                                           data-trace-time="<?php echo $query->trace_time; ?>">
                                <span
                                    class="diary-time"><?php echo date("g:i a", strtotime($query->timestamp)); ?></span>
                                </div>
                                <?php
                                $perc_activity = get_trace_activity($query->clicks_mouse, $query->clicks_keyboard, $query->trace_time);
                                ?>
                                <div class="col-xs-6 no-padding-l">
                                    <div class="diary-activity-status" data-toggle="tooltip"
                                         data-placement="top"
                                         title="<?php echo 'Mouse: ' . $query->clicks_mouse . '  Keyboard: ' . $query->clicks_keyboard ?>"><span
                                            class="diary-activity"
                                            style="width:<?php if ($perc_activity) {
                                                echo $perc_activity;
                                            } else {
                                                echo '0';
                                            } ?>%"><?php if ($perc_activity) {
                                                echo $perc_activity;
                                            } else {
                                                echo '0';
                                            } ?>%</span></div>
                                </div>

                            </div>
                        <?php
                        } else {
                            ?>
                            <div class="empty-img-confirm">
                            </div>
                            <div class="row info-block-empty">
                            </div>
                        <?php
                        }

                        ?>


</div>