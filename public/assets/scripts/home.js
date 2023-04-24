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

    mdo.style.display = value.value == "mdoUserCount" ? "none" : "block";
    dept.style.display = value.value == "ministryUserEnrolment" ? "none" : "block";
    org.style.display = value.value == "ministryUserEnrolment" ? "none" : "block";
}

function enable_disable_course(value) {
    course = document.getElementById("tbl-course");
    if (value.value == "courseEnrolmentCount" || value.value == "programEnrolmentCount") {
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
        success: function(data) {
            html = '<ul>';
            for (var count = 0; count < data.length; count++) {

                html += '<li>' + data[count].org_name + '</li>';

            }
            html += '</ul>';
            $('#display').html(html);
        }
    });


}