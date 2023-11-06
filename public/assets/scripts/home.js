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
    org = document.getElementById('orgname');

    if (value.value == "mdoUserCount" || value.value == "orgList" || value.value == "userList"|| value.value == "enrolmentPercentage") {
        mdo.style.display = "none";
        org.style.display = "none";

    }
    else {
        mdo.style.display = "block";

    }

    if (value.value == "ministryUserEnrolment" || value.value == "orgHierarchy" || value.value == "ministryUserList") {
        org.placeholder = "Search Ministry/State";
        org.value = "";

    }

    else {
        org.placeholder = "Search Organisation";
        org.value = "";

    }

}

function enable_disable_course(value) {
    course = document.getElementById("tbl-course");
    coursename = document.getElementById("coursename");
    if (value.value == "courseEnrolmentCount" || value.value == "programEnrolmentCount" || value.value == "liveCourses" || value.value == "underPublishCourses" || value.value == "underReviewCourses" || value.value == "draftCourses" || value.value == "cbpProviderWiseCourseCount"  || value.value == "rozgarMelaReport"  || value.value == "rozgarMelaSummary") {
        course.style.display = "none";
        course.value = "";
    } else if (value.value == "courseEnrolmentReport" || value.value == "courseMinistrySummary") {
        course.style.display = "block";
        coursename.placeholder = "Search Course";
        coursename.value = "";
    } else if (value.value == "programEnrolmentReport") {
        course.style.display = "block";
        coursename.placeholder = "Search Program";
        coursename.value = "";
    } else if (value.value == "collectionEnrolmentReport" || value.value == "collectionEnrolmentCount") {
        course.style.display = "block";
        coursename.placeholder = "Search Curated Collection";
        coursename.value = "";
    } else {
        course.style.display = "none";
        coursename.value = "";
    }

}


function enable_disable_program(value) {
    course = document.getElementById("tbl-program");
    if (value.value == "atiWiseOverview") {
        course.style.display = "none";
        course.value = "";
    } else {
        course.style.display = "block";
        course.value = "";
    }

}

function enable_disable_user(value) {
    user = document.getElementById("tbl-user");
    
    if (value.value == "userList" ||value.value == "userEnrolmentFull" || value.value == "userEnrolmentSummary") {
        user.style.display = "none";
    } else if (value.value == "userProfile" || value.value == "userEnrolment") {
        user.style.display = "block";
        
    }

}

function enable_disable_top(value) {
    course = document.getElementById("tbl-top-course");
    month = document.getElementById("tbl-top-monthyear");
    coursename = document.getElementById("topcoursename");
    competency = document.getElementById("tbl-top-competency");

    if (value.value == "topOrgCourseWise") {
        course.style.display = "block";
        month.style.display = "none";
        competency.style.display = "none";
        coursename.placeholder = "Search Course";
        coursename.value = "";
    } else if (value.value == "topOrgProgramWise") {
        course.style.display = "block";
        month.style.display = "none";
        competency.style.display = "none";
        coursename.placeholder = "Search Program";
        coursename.value = "";
    } else if (value.value == "topOrgCollectionWise") {
        course.style.display = "block";
        month.style.display = "none";
        competency.style.display = "none";
        coursename.placeholder = "Search Curated Collection";
        coursename.value = "";
    } else if (value.value == "topCourseInMonth"){
        course.style.display = "none";
        month.style.display = "block";
        competency.style.display = "none";
        
    } else if (value.value == "topCompetency"){
        course.style.display = "none";
        month.style.display = "none";
        competency.style.display = "block";
        
    } else {
        course.style.display = "none";
        month.style.display = "none";
        competency.style.display = "none";
        
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