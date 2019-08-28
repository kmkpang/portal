<?php
/**
 * Created by PhpStorm.
 * User: Lian
 * Date: 7/2/14
 * Time: 4:40 AM
 */

?>
<script>
    $(document).ready(function () {
        $('#contact-form').submit(function (e) {
            e.preventDefault();

            var data = {};
            $('#contact-form').find('input, textarea').each(function (i, el) {
                var name = $(el).attr('name');
                var val = $(el).val();
                if (name && val)
                    data[name] = val;
            });

            $('#submit')
                .val('Sending...')
                .attr('disabled','disabled');

            $.ajax({
                url: 'api/v1/contacts/saveContact',
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

            return false;
        });
    });
</script>

<form id="contact-form" action="" method="post">
    <div>
        <label for="name">Name</label>
        <input id="name" name="contact_name" type="text" placeholder="Your name"/>
    </div>

    <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="contact_email" placeholder="Your email"/>
    </div>

    <div>
        <label for="phone">Phone</label>
        <input id="phone" type="tel" name="contact_phone" placeholder="Your phone"/>
    </div>

    <div>
        <label for="subject">Subject</label>
        <textarea id="subject" name="message" placeholder="Your questions and comments">
        </textarea>
    </div>

    <div>
        <input id="submit" type="submit" value="Send"/>
    </div>

</form>
