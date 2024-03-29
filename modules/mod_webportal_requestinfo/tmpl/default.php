<?php
/**
 * Created by PhpStorm.
 * User: Lian
 * Date: 7/2/14
 * Time: 4:40 AM
 */

$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . 'assets/bower_components/jquery/dist/jquery.js');
// $doc->addScript('http://remax.softverk.co.th/assets/bower_components/jquery/dist/jquery.js');
?>
<script>
    $(document).ready(function () {
        $('#contact-form').submit(function (e) {
            e.preventDefault();

            var data = {};
            $('#contact-form').find('input, textarea , select').each(function (i, el) {
                var type = el.nodeName.toLowerCase();
                var inputtype = $(el).attr('type');
                var name = $(el).attr('name');
                var val = $(el).val();

                console.log(type + ' , ' + inputtype + ' , ' + name + " , " + val);
                if (type == 'textarea' || inputtype == 'text') {
                    var val = $(el).val();
                    if (name && val)
                        data[name] = val;
                }
                else if (inputtype == 'checkbox' && $(el).is(':checked')) {

                    var val = $(el).val();

                    if (typeof data[name] == 'undefined')//first time
                    {
                        data[name] = val;
                    }
                    else
                        data[name] = data[name] + " , " + val;
                }
                else if (inputtype == 'radio' && $(el).is(':checked')) {

                    var val = $(el).val();

                    if (typeof data[name] == 'undefined')//first time
                    {
                        data[name] = val;
                    }
                    else
                        data[name] = data[name] + " , " + val;
                }
                else if(type == 'select'){
                    data[name] = val;
                }
            });

            $('#submit')
                .val('Sending...')
                .attr('disabled', 'disabled');

            $.post('<?php echo JUri::base()?>' + 'api/v1/requestinfo/sendMailToDefaultCompany',

                data, function (d) {
                    $('#contact-form').html('<span>Thank you for contacting us! We will be right with you soon.</span><br/><a href="<?php echo JUri::base()?>">Back to homepage</a>');
                });

            /**
             *  -------------- $.ajax does not work in chrome????
             $.ajax({
                url: documentRoot+ 'api/v1/requestinfo/sendMailToDefaultCompany',
                method: 'post',
                dataType: 'json',
                contentType: 'application/json;charset=utf-8',
                data: JSON.stringify(data)
            }).success(function () {
                $('#contact-form').html('<span>Thank you for contacting us! We will be right with you soon.</span><br/><a href="<?php echo JUri::base()?>">Back to homepage</a>');
            }).then(function () {
                $('#submit').val('Send');
                $('#submit').removeAttr('disabled');
            });
             **/
            return false;
        });
    });
</script>
<div class="row contact--row">
    <div class="row pad">
        <div class="columns large-12">
            <?php
            $modules = JModuleHelper::getModules('contactheader');
            foreach ($modules as $module) {
                echo JModuleHelper::renderModule($module);
            }
            ?>
        </div>
        <div class="columns large-12"></div>
    </div>
    <div class="row pad">
        <h3 style="font-size: 24px; margin: 0 13px 1em">Request more information</h3>

        <div class="columns large-9">
            <form id="contact-form" action="" method="post">
                <div class="contact__form--row">
                    <label for="name" class="contact__textbox-label">First Name</label>

                    <div class="input-textbox--wrapper">
                        <input id="name" name="contact_first_name" type="text" placeholder="Your first name"/>
                    </div>
                </div>

                <div class="contact__form--row">
                    <label for="name" class="contact__textbox-label">Last Name</label>

                    <div class="input-textbox--wrapper">
                        <input id="name" name="contact_last_name" type="text" placeholder="Your last name"/>
                    </div>
                </div>

                <div class="contact__form--row">
                    <label for="email" class="contact__textbox-label">Email</label>

                    <div class="input-textbox--wrapper">
                        <input id="email" type="text" name="contact_email" placeholder="Your email"/>
                    </div>
                </div>
                <div class="contact__form--row">
                    <label for="email" class="contact__textbox-label">Phone</label>

                    <div class="input-textbox--wrapper">
                        <input id="email" type="text" name="contact_phone" placeholder="Your phone number"/>
                    </div>
                </div> 

                <div class="contact__form--row">
                    <label for="province" class="contact__textbox-label">Province</label>

                    <div class="input-textbox--wrapper">
                        <input id="province" type="text" name="contact_province" placeholder="Your province"/>
                    </div>
                </div>

                <hr>

                <div class="contact__form--row">
                    <label for="province_of_interest" class="contact__textbox-label">Province of Interest</label>

                    <div class="input-textbox--wrapper">
                        <input id="province_of_interest" type="text" name="contact_province_of_interest"
                               placeholder="Your province of interest"/>
                    </div>
                </div>

                <div class="contact__form--row">
                    <label for="district" class="contact__textbox-label">District of Interest</label>

                    <div class="input-textbox--wrapper">
                        <input id="district" type="text" name="contact_district_of_interest"
                               placeholder="Your district of interest"/>
                    </div>
                </div>

                <div class="contact__form--row">
                    <label for="subject" class="contact__textbox-label">Comments / Questions</label>

                    <div class="input-textbox--wrapper">
                        <textarea class="contact__textbox-textarea" id="subject" name="message"
                                  placeholder="Your questions and comments"></textarea>
                    </div>
                </div>


                <div class="columns large-15">
                    <div class="contact__form--row">
                        <label for="knowus" class="contact__textbox-label">How do you know about us?</label>
                        <select name="how_do_you_know_us">
                            <option value="Google">Google</option>
                            <option value="Facebook">Facebook</option>
                            <option value="Magazine">Magazine</option>
                            <option value="Friend">Friend</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="contact__form--row">
                        <label for="subject" class="contact__textbox-label">Interested to be</label>
                        <input id="interested_to_be" type="radio" name="interested_to_be" value="Broker"/> Broker &nbsp;&nbsp;
                        <input id="interested_to_be" type="radio" name="interested_to_be" value="Agent"/> Agent &nbsp;&nbsp;
                        <input id="interested_to_be" type="radio" name="interested_to_be" value="Not sure"/> Not sure
                        &nbsp;&nbsp;
                    </div>
                    <div class="contact__form--row">
                        <label for="subject" class="contact__textbox-label">Any real estate experience?</label>
                        <input id="interested_to_be" type="radio" name="previous_realestate_experience" value="Yes"/> Yes &nbsp;&nbsp;
                        <input id="interested_to_be" type="radio" name="previous_realestate_experience" value="No"/> No &nbsp;&nbsp;
                    </div>
                </div>

                <div class="contact__form--row">
                    <input id="submit" type="submit" value="Send" class="input-submit"/>
                </div>

            </form>
        </div>


    </div>
</div>
