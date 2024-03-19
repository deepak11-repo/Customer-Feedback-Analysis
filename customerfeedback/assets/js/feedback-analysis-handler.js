jQuery(document).ready(function ($) {
  
  $("#metric-table").DataTable({
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: "Download Excel",
        filename: "feedback_metric_report",
        exportOptions: {
          modifier: {
            page: "current",
          },
        },
      },
    ],
  });

  $("#general-table").DataTable({
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: "Download Excel",
        filename: "feedback_general_report",
        exportOptions: {
          modifier: {
            page: "current",
          },
        },
      },
    ],
  });

});

function toggleDiv() {
  var category = document.getElementById("categorySelect").value;
  var metricDiv = document.getElementById("metric");
  var generalDiv = document.getElementById("general");

  if (category === "Metric") {
      metricDiv.style.display = "block";
      generalDiv.style.display = "none";
  } else if (category === "General") {
      metricDiv.style.display = "none";
      generalDiv.style.display = "block";
  }
}
