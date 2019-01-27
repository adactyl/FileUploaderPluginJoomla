// @author          Denis Vorontsov
// @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later

jQuery("document").ready(function () {

    jQuery("#getFile").change(function () {

        //disable the input and add an search gif
        jQuery("#getFile").prop('disabled', true);
        jQuery("#iu_result").html('<img src="./loading.gif"/>');

        var files = getFile.files;
        var myFormData = new FormData();
        jQuery.each(files, function (key, value) {
            myFormData.append(key, value);
        });

        jQuery.ajax(
            {
                url: "index.php?option=com_ajax&plugin=filesupload&format=raw",
                type: "POST",
                data: myFormData,
                cache: false,
                // dataType: 'json',
                processData: false, // Не обрабатываем файлы (Don't process the files)
                contentType: false, // Так jQuery скажет серверу что это строковой запрос
                success: function (data) {
                    var answer = JSON.parse(data);
                    if (answer.error) {
                        jQuery("#iu_notice").html('<span style="color:red;font-weight:bold">' + answer.error + '</span>');
                        jQuery("#getFile").val("");
                        jQuery("#getFile").prop('disabled', false);
                        return
                    }
                    var savedValueObj = {
                        relDirPath: answer.relDirPath,
                        uploadDirPath: answer.uploadDirPath
                    };
                    var savedValueJSON = JSON.stringify(savedValueObj);
                    jQuery("#setfieldval").val(savedValueJSON);
                    jQuery("#iu_notice").html('<span style="color:darkgreen;">Файлы загружены! Сохраните изменения</span>');
                }
            }
        );
    });
});