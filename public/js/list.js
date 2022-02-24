$(document).ready(function(e){
    const endpoint = "./api/enquiries";

    $.get(endpoint, (records)=>{
        let rows = "";
        if (records.length > 0) {
            records.forEach(enquiry => {
                rows += `
                <tr>
                    <td scope="row">${enquiry.first_name}</td>
                    <td>${enquiry.last_name}</td>
                    <td>${enquiry.email}</td>
                    <td>${enquiry.subject}</td>
                    <td><a href="./enquiry/${enquiry.slug}">Read</a></td>
                </tr>`
            });
        }
        $("tbody").html(rows);
    })
});