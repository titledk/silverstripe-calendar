(function($) {
    $(function () {
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
            var json = attendeeModal.attr('data-attendees');
            var attendees = JSON.parse(json);
            console.log('Attendees var, parsed from JSON', attendees);
            attendees.push(attendee);
            console.log('Pushed', attendees);
            console.log('Stringify: ', JSON.stringify(attendees));
            attendeeModal.attr('data-attendees', JSON.stringify(attendees));
            $('#attendee-modal').modal('hide');
            show_attendees();

        });

        function show_attendees()
        {
            var attendeeModal = $('#attendee-modal');
            var json = attendeeModal.attr('data-attendees');
            console.log('attendees json', json);

            var attendees = JSON.parse(json);
            console.log('Attendees var, parsed from JSON, show method:', attendees);

            //attendees-list
            var html='<table><th>Title</th><th>Name</th><th>Company</th><th>Email</th><th>Phone</th>';
            var arrayLength = attendees.length;
            console.log('ATTENDEES', arrayLength);
            var ctr=0;
            for (var i = 0; i < arrayLength; i++) {
                ctr = i;
                var attendee = attendees[i];
                console.log(attendee);
                html = html + "<tr id='attendee_"+ i+"'>";
                html = html + "<td>" + attendee.title + "</td>";
                html = html + "<td>" + attendee.first_name + ' ' + attendee.surname + "</td>";
                html = html + "<td>" + attendee.company + "</td>";
                html = html + "<td>" + attendee.email + "</td>";
                html = html + "<td>" + attendee.phone + "</td>";
                html = html + "</tr>";
            }

            console.log('ctr', ctr);

            var attendeeID = 'attendee_' + ctr;
            console.log('ID', attendeeID);
            var attendeeElement = $("#" + attendeeID);
            console.log('ELEMENT', attendeeElement);
            console.log('POS', $('#add-attendee-button').offset().top);

            $('html:not(:animated), body:not(:animated)').animate({
                scrollTop: $('#add-attendee-button').offset().top
            }, 100);
           // $('#attendee-form').highlight();

            /*
                        attendee.email = attendeeform.find('#email').val();
            attendee.phone = attendeeform.find('#phone').val();
             */

            html = html + "</table>";
            console.log('HTML', html);
            $('#attendees-list').html(html);
        }


    });
})(jQuery);
