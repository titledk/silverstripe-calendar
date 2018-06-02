(function($) {
    $(function () {

        $(document).ready(function() {
           show_attendees();
        });
//$('ul').on('click', "li.bibeintrag", function(){
//     alert('myattribute =' + $(this).attr('myattribute'));
// });
        $('form').on('click', '.attendee-edit', function() {
            var id = $(this).parent().parent().attr('id');
            console.log('Clicked edit ', id);
            var json = $('#AttendeesJSON').val();
            console.log('attendees json', json);

            var attendees = JSON.parse(json);
        })

        $('form').on('click', '.attendee-delete', function() {
            if (confirm("Do you wish to delete this attendee?")) {
                var id = $(this).parent().parent().attr('id');
                console.log('Clicked delete ', id);
                var number = id.split('_')[1];
                var json = $('#AttendeesJSON').val();
                console.log('attendees json', json);

                var attendees = JSON.parse(json);
                var newAttendees = [];
                for (var i = 0; i < attendees.length; i++) {
                    if (i != number) {
                        attendee = attendees[i];
                        newAttendees.push(attendee);
                    }
                }

                $('#AttendeesJSON').val(JSON.stringify(newAttendees));

                console.log('New attendees', newAttendees);
                show_attendees(false);
            };



        })

        // styling
        $('#add-attendee-button').addClass('btn btn-primary float-sm-right');

        // clear the form to allow adding of a new attendee
        $('#add-attendee-button').on('click', function() {
            $('#attendee-modal').modal();
        });

        $('#add-attendee-modal-button').on('click', function() {
            var attendeeform = $('#attendee-form');
            var attendee = {};
            attendee.email = attendeeform.find('#email').val();
            attendee.first_name = attendeeform.find('#first-name').val();
            attendee.surname = attendeeform.find('#surname').val();
            attendee.phone = attendeeform.find('#phone').val();
            attendee.company = attendeeform.find('#company').val();
            attendee.title = attendeeform.find('#title').val();


            console.log('Attendee', attendee);

            var attendeeModal = $('#attendee-modal');
            var json = $('#AttendeesJSON').val();
            var attendees = JSON.parse(json);
            console.log('Attendees var, parsed from JSON', attendees);
            attendees.push(attendee);
            console.log('Pushed', attendees);
            console.log('Stringify: ', JSON.stringify(attendees));
            $('#AttendeesJSON').val(JSON.stringify(attendees));
            $('#attendee-modal').modal('hide');
            show_attendees(true);

        });

        /**
         *
         * @param reposition
         */
        function show_attendees(reposition)
        {
            var attendeeModal = $('#attendee-modal');
            var json = $('#AttendeesJSON').val();
            console.log('attendees json', json);

            var attendees = JSON.parse(json);
            console.log('Attendees var, parsed from JSON, show method:', attendees);

            //attendees-list
            var html='<table><th>Title</th><th>Name</th><th>Company</th><th>Email</th><th>Phone</th>';
            var arrayLength = attendees.length;
            $('#PaymentRegistrationForm_paymentregisterform_NumberOfTickets').val(arrayLength);
            console.log('ATTENDEES', arrayLength);
            var ctr=0;
            for (var i = 0; i < arrayLength; i++) {
                ctr = i;
                console.log(i);
                var attendee = attendees[i];
                console.log(attendee);
                html = html + "<tr id='attendee_"+ i+"'>";
                html = html + "<td>" + attendee.title + "</td>";
                html = html + "<td>" + attendee.first_name + ' ' + attendee.surname + "</td>";
                html = html + "<td>" + attendee.company + "</td>";
                html = html + "<td>" + attendee.email + "</td>";
                html = html + "<td>" + attendee.phone + "</td>";
                html = html + '<td><i class="fa fa-edit attendee-edit" aria-hidden="true"></i></td>';
                html = html + '<td><i class="fa fa-close attendee-delete" aria-hidden="true"></i></td>';
                html = html + "</tr>";
            }

            console.log('ctr', ctr);

            var attendeeID = 'attendee_' + ctr;
            console.log('ID', attendeeID);
            var attendeeElement = $("#" + attendeeID);
            console.log('ELEMENT', attendeeElement);
            console.log('POS', $('#add-attendee-button').offset().top);

            if (reposition) {
                $('html:not(:animated), body:not(:animated)').animate({
                    scrollTop: $('#add-attendee-button').offset().top
                }, 100);
            }
            

            html = html + "</table>";
            console.log('HTML', html);
            $('#attendees-list').html(html);
        }


    });
})(jQuery);
