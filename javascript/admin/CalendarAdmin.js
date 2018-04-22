/*jslint white: true */
console.log('calendar admin');

(function($) {
    $(document).ready(function () {
        $.entwine(function ($) {
            $('#Form_ItemEditForm_AllDay').entwine({
                onmatch: function (e) {
                    updateStatus();
                    this._super();
                },
                onchange: function (e) {
                    updateStatus();
                    this._super();
                }
            });
        });Form_ItemEditForm_DurationGroup_Holder

        function updateStatus() {
            var allDay = $('#Form_ItemEditForm_AllDay').is(":checked");
            console.log('all day', allDay);
            if (allDay == true) {
                console.log('all day is yes');
                $('#Form_ItemEditForm_Duration_Holder').hide();
                $('#Form_ItemEditForm_TimeFrameType_1').hide();
                $('#Form_ItemEditForm_TimeFrameType_1').parent().hide();
                $("#Form_ItemEditForm_TimeFrameType_2").prop("checked", true);



                //  $('#Form_ItemEditForm_DurationGroup_Holder').hide();
            } else {
                $('#Form_ItemEditForm_Duration_Holder').show();
                $('#Form_ItemEditForm_TimeFrameType_1').show();
                $('#Form_ItemEditForm_TimeFrameType_1').parent().show();
                // $('Form_ItemEditForm_DurationGroup_Holder').attr('style', 'display:block;');
            }


        }
    })
})(jQuery);

