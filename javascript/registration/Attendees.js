(function($) {
    $(function () {

        $(document).ready(function() {
            $('#add-attendee-button').addClass('btn btn-primary float-sm-right');

            show_attendees();

            $("#attendee-form").on('submit', function(e){
                e.preventDefault();
                var form = $("#attendee-form");
                form.parsley().validate();

                if (form.parsley().isValid()){
                    alert('valid');
                } else {
                    alert('not valid');
                }
            });


        });

        // Show edit form
        $('form').on('click', '.attendee-edit', function() {
            var id = $(this).parent().parent().attr('id');
            var position = id.split('_')[1];
            $('#update-attendee-modal-button').attr('data-position', position);
            var json = $('#AttendeesJSON').val();
            var attendees = [];
            if (json != '') {
                attendees = JSON.parse(json);
            }
            var attendee = attendees[position];
            console.log('Editing attendee ', attendee);

            var attendeeform = $('#attendee-form');
            attendeeform.find('#email').val(attendee.email);
            attendeeform.find('#first-name').val(attendee.first_name);
            attendeeform.find('#surname').val(attendee.surname);
            attendeeform.find('#phone').val(attendee.phone);
            attendeeform.find('#company').val(attendee.company);
            attendeeform.find('#title').val(attendee.title);

            $('#add-attendee-modal-button').hide();
            $('#update-attendee-modal-button').show();

            $('#attendee-modal').modal();
        })

        // delete a record from the JSON
        $('form').on('click', '.attendee-delete', function() {
            if (confirm("Do you wish to delete this attendee?")) {
                var id = $(this).parent().parent().attr('id');
                var position = id.split('_')[1];
                var json = $('#AttendeesJSON').val();
                var attendees = [];
                if (json != '') {
                    attendees = JSON.parse(json);
                }
                var newAttendees = [];
                for (var i = 0; i < attendees.length; i++) {
                    if (i != position) {
                        attendee = attendees[i];
                        newAttendees.push(attendee);
                    }
                }

                $('#AttendeesJSON').val(JSON.stringify(newAttendees));

                console.log('New attendees', newAttendees);
                show_attendees(false);
            };



        })

        // clear the form to allow adding of a new attendee
        $('#add-attendee-button').on('click', function() {
            console.log('Clear form values');
            var attendeeform = $('#attendee-form');
            attendeeform.find('#email').val('');
            attendeeform.find('#first-name').val('');
            attendeeform.find('#surname').val('');
            attendeeform.find('#phone').val('');
            attendeeform.find('#company').val('');
            attendeeform.find('#title').val('');

            $('#add-attendee-modal-button').show();
            $('#update-attendee-modal-button').hide();

            $('#attendee-modal').modal();
        });

        /**
         * Update an attendee
         */
        $('#update-attendee-modal-button').on('click', function() {

            var form = $("#attendee-form");
            form.parsley().validate();

            if (form.parsley().isValid()){
                console.log('Update');
                var position = $('#update-attendee-modal-button').attr('data-position');
                var json = $('#AttendeesJSON').val();
                var attendees = [];
                if (json != '') {
                    attendees = JSON.parse(json);
                }
                var attendeeform = $('#attendee-form');
                attendees[position].email = attendeeform.find('#email').val();
                attendees[position].first_name = attendeeform.find('#first-name').val();
                attendees[position].surname = attendeeform.find('#surname').val();
                attendees[position].phone = attendeeform.find('#phone').val();
                attendees[position].company = attendeeform.find('#company').val();
                attendees[position].title = attendeeform.find('#title').val();
                $('#attendee-modal').modal('hide');
                $('#AttendeesJSON').val(JSON.stringify(attendees));

                show_attendees(false);
            }
        });

        /**
         * Save an attendee
         */
        $('#add-attendee-modal-button').on('click', function() {
            var form = $("#attendee-form");
            form.parsley().validate();

            if (form.parsley().isValid()) {
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
                var attendees = [];
                console.log('ATT JSON T4', json);
                if (json != '') {
                    attendees = JSON.parse(json);
                }
                console.log('Attendees var, parsed from JSON', attendees);
                attendees.push(attendee);
                console.log('Pushed', attendees);
                console.log('Stringify: ', JSON.stringify(attendees));
                $('#AttendeesJSON').val(JSON.stringify(attendees));
                $('#attendee-modal').modal('hide');
                show_attendees(true);
            }
        });


        $('#PaymentRegistrationForm_paymentregisterform').submit(function(e) {
            var json = $('#AttendeesJSON').val();
            localStorage.setItem('attendees', json);
        });



        /**
         *
         * @param reposition
         */
        function show_attendees(reposition)
        {
            var attendeeModal = $('#attendee-modal');

            var jsonFromLocalStorage = localStorage.getItem('attendees');
            console.log('FROM LOCAL STOrAGE:', jsonFromLocalStorage);

            if (jsonFromLocalStorage !== null) {
                $('#AttendeesJSON').val(jsonFromLocalStorage);
                localStorage.removeItem('attendees');
            }


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
