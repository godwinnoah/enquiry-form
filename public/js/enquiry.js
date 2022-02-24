$(document).ready(function(){
    const slug = $("#enquiry-slug").val();

    const endpoint = `../api/enquiry/${slug}`;

    $.get(endpoint, (record)=>{
        $("#fullname").text(`${record.first_name} ${record.last_name}`);
        $("#email").text(record.email);
        $("#subject").text(record.subject);
        $("#message").html(record.message.replaceAll(/\n/g, "<br />"));
        $("#enquirybody").removeClass("d-none");
    })
})