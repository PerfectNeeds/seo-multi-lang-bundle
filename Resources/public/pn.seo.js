(function ($) {
    $.seo = function (element, options) {
        instance = this;
        var $element = $(element), // reference to the jQuery version of DOM element
                element = element;    // reference to the actual DOM element


        var defaults = {
            metaDescriptionMinLength: 120,
            metaDescriptionMaxLength: 160,
            seoTitleMinLength: 50,
            seoTitleMaxLength: 60,
            titleMinLength: 50,
            titleMaxLength: 60,
            descriptionMinWordsCount: 300
        };

        // this will hold the merged default, and user-provided options
        // plugin's properties will be available through this object like:
        // plugin.settings.propertyName from inside the plugin or
        // element.data('pluginName').settings.propertyName from outside the plugin, 
        // where "element" is the element the plugin is attached to;
        instance.settings = {};


        //inti ajax variable 
        var currentSlugRequest = null;
        var currentFocusKeywordRequest = null;
        var ckEditorEditor = null;
        var titleInput = $element.prev(".panel").find("input[name$='[title]']").first();
        var descriptionInput = $element.prev(".panel").find("textarea[name$='[description]']").first();
        var seoTitleInput = $element.find("input[name$='[title]']").first();
        var seoFocusKeywordInput = $element.find("input[name$='[focusKeyword]']").first();
        var seoMetaDescriptionInput = $element.find("textarea[name$='[metaDescription]']").first();
        var seoSlugInput = $element.find("input[name$='[slug]']");
        var seoStateHiddenInput = $element.find("input[name$='[state]']");
        var locale = $element.data('locale');

        var init = function () {
            // the plugin's final properties are the merged default and 
            // user-provided options (if any)
            instance.settings = $.extend({}, defaults, options);

            inintElements();
            validateInputes();

            if (descriptionInput !== null) {
                CKEDITOR.on('instanceReady', function (e) {
                    var descriptionId = descriptionInput.attr('id');
                    ckEditorEditor = CKEDITOR.instances[descriptionId];
                    countTinyMCEWord(ckEditorEditor);
                    ckEditorEditor.on('change', function (event) {
                        var ed = CKEDITOR.instances[descriptionId];//Value of Editor
                        ckEditorEditor = ed;
                        countTinyMCEWord(ed);
                    });
                });
            } else {
                removeAnalysisItem("analysis-4");
                removeAnalysisItem("analysis-11");
                removeAnalysisItem("analysis-12");
            }

            seoMetaDescriptionInput.trigger('keyup');
            seoTitleInput.trigger('keyup');
            seoSlugInput.trigger('keyup');
            seoFocusKeywordInput.trigger('keyup');
            arrangeAnalysisItemItems();
        };
        var inintElements = function () {
            seoTitleInput.data("max-length", instance.settings.seoTitleMaxLength);
            seoMetaDescriptionInput.data("max-length", instance.settings.metaDescriptionMaxLength);

            // check if page not content a description textarea
            if (descriptionInput.length == 0) {
                descriptionInput = null;
            }

            $element.find(".descriptionMinWordsCount").text(instance.settings.descriptionMinWordsCount);
            $element.find(".titleMaxLength").text(instance.settings.titleMaxLength);
            $element.find(".seoTitleMaxLength").text(instance.settings.seoTitleMaxLength);
            $element.find(".metaDescriptionMinLength").text(instance.settings.metaDescriptionMinLength);
            $element.find(".metaDescriptionMaxLength").text(instance.settings.metaDescriptionMaxLength);
            if ($element.find(".seoSnippetEdit").find(".form-error").length > 0) {
                $element.find('button.seoSnippetEditBtn').append(' <i class="icon-circle2 text-danger form-error-bullet"></i>')
            }
        };
        var validateInputes = function () {
            if (seoDebug === false) {
                return false;
            }

            if (typeof titleInput === "undefined" || titleInput.length == 0) {
                console.error("titleInput is not found");
            }
            if (descriptionInput === null) {
                console.warn("This page doesn't contain description input");
            }
            if (typeof seoTitleInput === "undefined" || seoTitleInput.length == 0) {
                console.error("seoTitleInput is not found");
            }
            if (typeof seoFocusKeywordInput === "undefined" || seoFocusKeywordInput.length == 0) {
                console.error("seoFocusKeywordInput is not found");
            }
            if (typeof seoMetaDescriptionInput === "undefined" || seoMetaDescriptionInput.length == 0) {
                console.error("seoMetaDescriptionInput is not found");
            }
            if (typeof seoSlugInput === "undefined" || seoSlugInput.length == 0) {
                console.error("seoSlugInput is not found");
            }
            if (typeof seoStateHiddenInput === "undefined" || seoStateHiddenInput.length == 0) {
                console.error("seoStateHiddenInput is not found");
            }

            if (seoTitleInput.data("max-length") != instance.settings.seoTitleMaxLength) {
                console.warn("Warning: Seo title max length is not mached")
            }
            if (seoMetaDescriptionInput.data("max-length") != instance.settings.metaDescriptionMaxLength) {
                console.warn("Warning: Seo title max length is not mached")
            }
        };

        // count tinymce word and calculate the density
        var countTinyMCEWord = function (ed) {
            //count tinymce word
            var wordCount = countWords(ed.getData());
            $element.find('.seoContentCount').text(wordCount);
            if (wordCount >= instance.settings.descriptionMinWordsCount) {
                changeAnalysisItemColor('analysis-4', 'success');
            } else if (wordCount > 0) {
                changeAnalysisItemColor('analysis-4', 'warning');
            } else if (wordCount === 0) {
                changeAnalysisItemColor('analysis-4', 'danger');
            }

            // calculate the density
            var content = decodeHtml(ed.getData()).toLowerCase();
            var focusKeywordValue = seoFocusKeywordInput.val().toLowerCase();
            var focusKeywordCount = content.split(focusKeywordValue).length - 1;
            var density = ((focusKeywordCount / wordCount) * 100);
            if (focusKeywordValue.length > 0 && wordCount > 0 && density > 0) {
                changeAnalysisItemColor('analysis-12', 'success');
                density = density.toFixed(2);
            } else {
                density = 0.00;
                focusKeywordCount = 0;
                changeAnalysisItemColor('analysis-12', 'danger');
            }
            $element.find('.seoDensity').text(density);
            $element.find('.seoDensityTime').text(focusKeywordCount);

            var html = $.parseHTML(ed.getData());
            if (html !== null) {
                $.each(html, function (i, el) {
                    if (el.nodeName === 'P') {
                        var checkFirstParagraph = el.innerText.includes(seoFocusKeywordInput.val());
                        if (checkFirstParagraph === true && seoFocusKeywordInput.val()) {
                            changeAnalysisItemColor('analysis-11', 'success');
                        } else {
                            changeAnalysisItemColor('analysis-11', 'danger');
                        }
                        return false;
                    }
                });
            }
        };

        // change color of an analysis item and reorder them
        var changeAnalysisItemColor = function (elementClass, color) {
            $element.find('.' + elementClass).removeClass('border-success border-warning border-danger').addClass('border-' + color);
            arrangeAnalysisItemItems();

            var success = $element.find('.analysisList li.border-success').length;
            var warning = $element.find('.analysisList li.border-warning').length;
            var danger = $element.find('.analysisList li').not('.hidden, .border-warning, .border-success').length;

            var statusColorEl = $element.find('.stateColor');
            statusColorEl.removeClass('text-danger text-warning text-success');
            if (success > warning && success > danger) {
                seoStateHiddenInput.val(3);
                statusColorEl.addClass('text-success');
            } else if (warning > success && warning > danger) {
                statusColorEl.addClass('text-warning');
                seoStateHiddenInput.val(2);
            } else {
                statusColorEl.addClass('text-danger');
                seoStateHiddenInput.val(1);
            }
        };


        // reorder analysis item
        var arrangeAnalysisItemItems = function () {
            $element.find('.analysisList li.border-warning').insertAfter($element.find('.analysisList li:last-child'));
            $element.find('.analysisList li.border-success').insertAfter($element.find('.analysisList li:last-child'));
        };


        // convert text to slug
        var convertToSlug = function (Text) {
            return Text.toString().toLowerCase()
                    .replace(/\s+/g, '-')           // Replace spaces with -
                    .replace(/[^\u0100-\uFFFF\w\-]/g, '-') // Remove all non-word chars ( fix for UTF-8 chars )
                    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                    .replace(/^-+/, '')             // Trim - from start of text
                    .replace(/-+$/, '');
        };

        var decodeHtml = function (encodedString) {
            var textArea = document.createElement('textarea');
            textArea.innerHTML = encodedString;
            return textArea.value;
        };

        var strip = function (html) {
            var tmp = document.createElement("div");
            tmp.innerHTML = html;

            if (tmp.textContent == "" && typeof tmp.innerText == "undefined") {
                return "";
            }

            return tmp.textContent || tmp.innerText;
        };

        var countWords = function (text) {
            var normalizedText = text.
                    replace(/(\r\n|\n|\r)/gm, " ").
                    replace(/^\s+|\s+$/g, "").
                    replace("&nbsp;", " ");

            normalizedText = strip(normalizedText);

            var words = normalizedText.split(/\s+/);

            for (var wordIndex = words.length - 1; wordIndex >= 0; wordIndex--) {
                if (words[wordIndex].match(/^([\s\t\r\n]*)$/)) {
                    words.splice(wordIndex, 1);
                }
            }

            return (words.length);
        };

        // remove extra spaces
        var filterText = function (text) {
            return text.replace('  ', ' ').trim();
        };
        //show analysis item
        var showAnalysisItem = function (elementId) {
            $element.find('.' + elementId).removeClass('hidden');
            $element.find('.' + elementId).insertBefore($element.find('.analysisList li:eq(0)'));
        };

        //hide analysis item
        var hideAnalysisItem = function (elementId) {
            $element.find('.' + elementId).addClass('hidden');
            $element.find('.' + elementId).insertAfter($element.find('.analysisList li:eq(-1)'));
        };
        var removeAnalysisItem = function (elementId) {
            $element.find('.' + elementId).remove();
        };
        // change input text color
        var changeInputTextColor = function (elementId, color) {
            if (color === '') {
                $element.find('.' + elementId).removeClass('text-danger');
            } else {
                $element.find('.' + elementId).removeClass('text-danger').addClass(color);

            }
        };

        // compare between focus keyword and title
        var focusKeywordVsTitle = function () {
            title = seoTitleInput.val();
            focusKeyword = seoFocusKeywordInput.val();
            if (title.length > 0 && focusKeyword.length > 0) {
                if (title.includes(focusKeyword)) {
                    hideAnalysisItem('analysis-5');
                    changeAnalysisItemColor('analysis-7', 'success');
                    return false;
                } else {
                    changeAnalysisItemColor('analysis-7', 'danger');
                }
            } else {
                changeAnalysisItemColor('analysis-7', 'danger');
            }
        };

        // compare between focus keyword and slug
        var focusKeywordVsSlug = function () {
            slug = seoSlugInput.val().replace(/[\W_]/g, ' ').replace(/\s+/g, '').trim().toLowerCase();
            focusKeyword = seoFocusKeywordInput.val().replace(/[\W_]/g, ' ').replace(/\s+/g, '').trim().toLowerCase();
            include = slug.includes(focusKeyword);
            if (include !== false && focusKeyword !== '') {
                changeAnalysisItemColor('analysis-8', 'success');
            } else {
                changeAnalysisItemColor('analysis-8', 'warning');
            }
        };
        var goToByScroll = function (element) {
            // Scroll
            $('html,body').animate({
                scrollTop: element.offset().top
            }, 'slow');
        }
        var snippetPreview = function (element) {
            var previewElementId = element.data('preview');

            var inputValue = element.val();
            var length = inputValue.length;

            if (length > 0) {
                $element.find('.' + previewElementId).text(inputValue);
            } else {
                var inputId = element.attr('id');
                if (inputId === seoTitleInput.attr("id")) {
                    $element.find('.' + previewElementId).text('[PAGE TITLE]');
                } else if (inputId === seoMetaDescriptionInput.attr("id")) {
                    $element.find('.' + previewElementId).text('Please provide a meta description by editing the snippet below.');
                }
            }
        }
        // event listeners
        $element.find("button.seoSnippetEditBtn").click(function () {
            $element.find(".seoSnippetEdit").slideToggle();
        });

        $element.find('.countLength').keyup(function () {
            var lengthBadge = $(this).parent().find('.lengthBadge');
            var maxLength = $(this).data('max-length');
            var lengthBadge = $(this).parent().find('.lengthBadge');

            var inputValue = $(this).val();
            var length = inputValue.length;

            // An error appears if the entry contains a new line
            if (inputValue.includes("\n")) {
                $(this).addClass('text-danger');
            } else {
                $(this).removeClass('text-danger');
            }

            lengthBadge.removeClass('label-warning label-warning label-default label-danger');
            if (length === 0) {
                lengthBadge.addClass('label-default');
            } else if (length === maxLength) {
                lengthBadge.addClass('label-success');
            } else if (length > maxLength) {
                lengthBadge.addClass('label-danger');
            } else {
                lengthBadge.addClass('label-warning');
            }

            lengthBadge.find('.length').text(length);
        });
        seoTitleInput.keyup(function () {
            value = $(this).val();
            length = value.length;
            focusKeywordVsTitle();
            if (length >= instance.settings.titleMinLength && length <= instance.settings.titleMaxLength) {
                changeAnalysisItemColor('analysis-10', 'success');
            } else {
                changeAnalysisItemColor('analysis-10', 'danger');
            }

            snippetPreview($(this));
        });

        titleInput.blur(function () {
            if (seoSlugInput.prop('value') === '') {
                var value = $(this).val();
                var slug = convertToSlug(value);
                seoSlugInput.val(slug);
                seoSlugInput.trigger('keyup');
            }
            if (seoTitleInput.prop('value') === '') {
                seoTitleInput.val($(this).val());
                seoTitleInput.trigger('keyup');
            }
        });
        // convert slug text to slugify format
        seoSlugInput.blur(function () {
            var value = filterText($(this).val());
            var slugify = convertToSlug(value);
            seoSlugInput.val(slugify);
            focusKeywordVsSlug();
        });
        // Check if  the slug  is used before
        seoSlugInput.keyup(function () {
            var previewElementId = $(this).data('preview');
            var value = filterText($(this).val());
            var slugify = convertToSlug(value);
            $element.find('.' + previewElementId).text(slugify);

            if (value.length > 0) {
                currentSlugRequest = $.ajax({
                    url: checkSlugIsUsedUrlAjax,
                    data: {
                        slug: slugify, seoId: seoId,
                        seoBaseRouteId: seoBaseRouteId,
                        locale:locale
                    },
                    beforeSend: function () {
                        seoSlugInput.parent().find(".form-control-feedback").removeClass("hidden");
                        if (currentSlugRequest !== null) {
                            currentSlugRequest.abort();
                        }
                    },
                    success: function (result) {
                        var validationLabel = seoSlugInput.parent().find('.validation-error-label');
                        if (validationLabel.length == 1) {
                            validationLabel.remove();
                        }
                        seoSlugInput.parent().find(".form-control-feedback").addClass("hidden");
                        if (result == 0) {
                            seoSlugInput.parent().removeClass('has-error');
                            seoSlugInput.parent().find('.help-block').remove();

                        } else {
                            seoSlugInput.parent().addClass('has-error');
                            seoSlugInput.parent().find('.help-block').remove();
                            var error = $('<span/>').addClass('help-block').text('This slug is used before');
                            seoSlugInput.parent().append(error);
                            goToByScroll($element);
                            $element.find(".seoSnippetEdit").slideDown();
                        }
                    }
                });
            }
        });
        // remove extra spaces between words
        seoMetaDescriptionInput.blur(function () {
            var value = $(this).val();
            $(this).val(filterText(value));
            snippetPreview($(this));
        });
        seoMetaDescriptionInput.keyup(function () {
            var value = filterText($(this).val());
            var length = value.length;
            if (length === 0) {
                showAnalysisItem('analysis-2');
                hideAnalysisItem('analysis-3');
                hideAnalysisItem('analysis-6');
            } else {
                if (length < instance.settings.metaDescriptionMinLength) {
                    showAnalysisItem('analysis-3');
                    hideAnalysisItem('analysis-6');
                } else if (length >= instance.settings.metaDescriptionMinLength && length < instance.settings.metaDescriptionMaxLength) {
                    hideAnalysisItem('analysis-3');
                    showAnalysisItem('analysis-6');
                    changeAnalysisItemColor('analysis-6', 'success');
                } else if (length === instance.settings.metaDescriptionMaxLength) {
                    hideAnalysisItem('analysis-3');
                    changeAnalysisItemColor('analysis-6', 'success');
                } else {
                    changeInputTextColor($(this).attr('id'), 'text-danger');
                    hideAnalysisItem('analysis-3');
                    hideAnalysisItem('analysis-6');
                }
                hideAnalysisItem('analysis-2');
            }
            snippetPreview($(this));
        });
        seoFocusKeywordInput.keyup(function () {
            var value = $(this).val().trim();
            if (value.length === 0) {
                showAnalysisItem('analysis-1');
                hideAnalysisItem('analysis-5');
                hideAnalysisItem('analysis-9');
            } else {
                showAnalysisItem('analysis-9');
                showAnalysisItem('analysis-5');
                hideAnalysisItem('analysis-1');
            }
            $element.find('.copyFocusKeyword').text(value);

            currentFocusKeywordRequest = $.ajax({
                url: focusKeywordUrlAjax,
                data: {focusKeyword: value, seoId: seoId},
                beforeSend: function () {
                    if (currentFocusKeywordRequest !== null) {
                        currentFocusKeywordRequest.abort();
                    }
                }, success: function (result) {
                    if (result == 0) {
                        changeAnalysisItemColor('analysis-9', 'success');
                    } else {
                        changeAnalysisItemColor('analysis-9', 'danger');
                    }
                }
            });
            if (ckEditorEditor !== null) {
                countTinyMCEWord(ckEditorEditor);
            }
            focusKeywordVsTitle();
            focusKeywordVsSlug();
        });
        init();
    };
    // add the plugin to the jQuery.fn object
    $.fn.seoPlugin = function (options) {
        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('seoPlugin')) {

                // create a new instance of the plugin
                // pass the DOM element and the user-provided options as arguments
                var plugin = new $.seo(this, options);

                // in the jQuery version of the element
                // store a reference to the plugin object
                // you can later access the plugin and its methods and properties like
                // element.data('pluginName').publicMethod(arg1, arg2, ... argn) or
                // element.data('pluginName').settings.propertyName
                $(this).data('seoPlugin', plugin);

            }

        });

    }
})(jQuery);