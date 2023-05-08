function toggleMenu() {
    var menuItems = document.getElementsByClassName('menu-item');
    for (var i = 0; i < menuItems.length; i++) {
        var menuItem = menuItems[i];
        menuItem.classList.toggle("hidden");
    }
}

function openTab(evt, reprotName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(reprotName).style.display = "block";
    evt.currentTarget.className += " active";
}

function enable_disable_mdo(value) {

    mdo = document.getElementById('tbl');
    dept = document.getElementById('dept');
    org = document.getElementById('org');

    if (value.value == "mdoUserCount" || value.value == "orgList" || value.value == "userList")
        mdo.style.display = "none"

    else
        mdo.style.display = "block";

    if (value.value == "ministryUserEnrolment" || value.value == "orgHierarchy") {
        dept.style.display = "none";
        org.style.display = "none";
    }

    else {
        dept.style.display = "block";
        org.style.display = "block";
    }

}

function enable_disable_course(value) {
    course = document.getElementById("tbl-course");
    if (value.value == "courseEnrolmentCount" || value.value == "programEnrolmentCount" || value.value == "liveCourses" || value.value == "underPublishCourses" || value.value == "underReviewCourses" || value.value == "draftCourses" ) {
        course.style.display = "none";
    } else {
        course.style.display = "block";
    }

}

function enable_disable_program(value) {
    course = document.getElementById("tbl-program");
    if (value.value == "atiWiseOverview") {
        course.style.display = "none";
    } else {
        course.style.display = "block";
    }

}





function getSuggestions(value) {
    var searchKey = value.value;
    var action = 'search';

    //alert(value.value);
    display = document.getElementById('display');
    $.ajax({
        url: "<?php echo base_url('/action'); ?>",
        method: "POST",
        data: {
            key: searchKey,
            action: action
        },
        dataType: "JSON",
        success: function (data) {
            html = '<ul>';
            for (var count = 0; count < data.length; count++) {

                html += '<li>' + data[count].org_name + '</li>';

            }
            html += '</ul>';
            $('#display').html(html);
        }
    });


}


function getOrgName(org) {

}


function tableToCSV(tableID, filename) {
    // Variable to store the final csv data
    var csv_data = [];

    // Get each row data
    var rows = document.getElementsByTagName('tr');
    //  alert(rows);

    for (var i = 0; i < rows.length; i++) {

        // Get each column data
        var cols = rows[i].querySelectorAll('td,th');

        // Stores each csv row data
        var csvrow = [];
        for (var j = 0; j < cols.length; j++) {

            // Get the text data of each cell
            // of a row and push it to csvrow
            csvrow.push(cols[j].innerHTML);
        }

        // Combine each column value with comma
        csv_data.push(csvrow.join(","));
    }

    // Combine each row data with new line character
    csv_data = csv_data.join('\n');

    // Call this function to download csv file 
    downloadCSVFile(csv_data);

}

function downloadCSVFile(csv_data) {

    // Create CSV file object and feed
    // our csv_data into it
    CSVFile = new Blob([csv_data], {
        type: "text/csv"
    });

    // Create to temporary link to initiate
    // download process
    var temp_link = document.createElement('a');

    // Download csv file
    temp_link.download = "GfG.csv";
    var url = window.URL.createObjectURL(CSVFile);
    temp_link.href = url;

    // This link should not be displayed
    temp_link.style.display = "none";
    document.body.appendChild(temp_link);

    // Automatically click the link to
    // trigger download
    temp_link.click();
    document.body.removeChild(temp_link);
}