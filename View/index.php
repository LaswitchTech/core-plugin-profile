<!--
  Core Framework - View File

  @license    MIT (https://mit-license.org/)
  @author     Louis Ouellet <louis@laswitchtech.com>
-->
<div class="col-12" id="layout"></div>
<script>
    $(document).ready(function(){

        // Ajax Request
        $.ajax({
            url: '/endpoint.php/profile/fetch',
            type: 'GET',dataType: 'json',
            success: function(response) {
                console.log(response);

                // Set the element
                var element = $('#layout');

                // Setup the layout
                element.row = $(document.createElement('div')).addClass('row').appendTo(element);
                element.col1 = $(document.createElement('div')).addClass('col-12 col-md-6 col-lg-4').appendTo(element.row);
                element.col2 = $(document.createElement('div')).addClass('col-12 col-md-6 col-lg-8').appendTo(element.row);

                var contacts = [];
                for(const [key, value] of Object.entries(response.objects.contacts ?? {})){
                    var text = value.vcard.name;
                    if(value.vcard.title != null){
                        text += ' - ' + value.vcard.title;
                    }
                    contacts.push({id:value.vcard.id,text:text});
                }

                // Create the Profile Card
                builder.Component(
                    "card",
                    element.col1,
                    {
                        icon: "person",
                        title: builder.Locale.get('Details'),
                    },
                    function(card,component){

                        // Styling
                        component.body.addClass('d-flex flex-column justify-content-center align-items-center');

                        // Insert the user's profile picture
                        component.body.avatar = $(document.createElement('div')).addClass('rounded-circle border border-3 border-light d-flex justify-content-center align-items-center position-relative').css({"height": "256px", "width": "256px"}).appendTo(component.body);
                        component.body.avatar.img = $(document.createElement('img')).attr({
                            "class": "rounded-circle",
                            "src": '/avatar?username=<?= $this->Auth->user()->username ?>&size=256',
                            "data-type": "avatar",
                            "data-vcard": response.vcards.user.id,
                            "style": "max-height: 250px; max-width: 250px; height: 250px; width: 250px; object-fit: contain; object-position: center;",
                        }).appendTo(component.body.avatar);
                        component.body.avatar.btn = $(document.createElement('button')).attr({
                            "type": "button",
                            "class": "btn btn-sm btn-info fs-5 rounded-circle position-absolute",
                            "style": "transition: all 0.5s ease-in-out; height: 48px!important; width: 48px!important; bottom: 8px; right: 8px;",
                        }).html('<i class="bi bi-upload"></i>').appendTo(component.body.avatar);
                        component.body.avatar.btn.click(function(){
                            vCardModalAvatar(response.vcards.user);
                        });

                        // Insert the user's name
                        component.body.name = $(document.createElement('div')).addClass('position-relative mt-2 text-center').appendTo(component.body);
                        component.body.name.string = $(document.createElement('h2')).addClass('fw-lighter m-0').text(response.vcards.user.name).appendTo(component.body.name);
                        component.body.name.btn = $(document.createElement('button')).attr({
                            "type": "button",
                            "class": "btn btn-sm btn-warning fs-5 rounded-circle position-absolute",
                            "style": "transition: all 0.5s ease-in-out; height: 48px!important; width: 48px!important; top: calc(50% - 24px); right: -56px;",
                        }).html('<i class="bi bi-pencil"></i>').appendTo(component.body.name);
                        component.body.name.btn.click(function(){
                            vCardModalEdit(response.vcards.user);
                        });

                        // Insert the user's organization
                        component.body.organization = $(document.createElement('div')).addClass('position-relative mt-2 text-center').appendTo(component.body);
                        component.body.organization.string = $(document.createElement('h4')).addClass('fw-lighter m-0').text(response.vcards.organization.name).appendTo(component.body.organization);
                        component.body.organization.btn = $(document.createElement('button')).attr({
                            "type": "button",
                            "class": "btn btn-sm btn-primary fs-5 rounded-circle position-absolute",
                            "style": "transition: all 0.5s ease-in-out; height: 48px!important; width: 48px!important; top: calc(50% - 24px); right: -56px;",
                        }).html('<i class="bi bi-person-vcard"></i>').appendTo(component.body.organization);
                        component.body.organization.btn.click(function(){
                            vCardModal(response.vcards.organization.id);
                        });
                    },
                );

                // Create a Tabs component
                builder.Component(
                    "tabs",
                    element.col2,
                    {
                        class: {
                            navbar: 'nav-pills',
                        },
                    },
                    function(tabs,card){
                        card._component.body.removeClass('card-body');
                        // tabs.add(
                        //     'activities',
                        //     {
                        //         icon: "activity",
                        //         label: builder.Locale.get("Activity"),
                        //     },
                        //     function(tab,nav){
                        //         tab.addClass('px-4 py-3');
                        //         card.activities = tab;
                        //         EventFeed(response.objects.events ?? {}, tab);
                        //     },
                        // );
                        tabs.add(
                            'contacts',
                            {
                                icon: "person-vcard",
                                label: builder.Locale.get("Contacts"),
                            },
                            function(tab,nav){
                                card.contacts = tab;
                                ContactsFeed(response.objects.contacts ?? {}, tab, {
                                    "address": response.vcards.user.address,
                                    "city": response.vcards.user.city,
                                    "country": response.vcards.user.country,
                                    "state": response.vcards.user.state,
                                    "zipcode": response.vcards.user.zipcode,
                                    "locale": response.vcards.user.locale,
                                    "phone": response.vcards.user.phone,
                                    "targetTable": "users",
                                    "targetId": response.id,
                                });
                            },
                        );
                        tabs.add(
                            'notes',
                            {
                                icon: "stickies",
                                label: builder.Locale.get("Notes"),
                            },
                            function(tab,nav){
                                card.notes = tab;
                                NotesFeed(response.objects.notes ?? {}, tab, 'users', response.id);
                            },
                        );
                    },
                );
            }
        });
    });
</script>
