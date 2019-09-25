@extends('layout')

@section('content')

    <? if( auth()->user()["username"] ):?>

        <script>

            // ---Wait until doc loaded, and then run Function 'canvasIMG'---
            document.addEventListener('DOMContentLoaded', function ()
                {
                    var urlAvatar   = 'avatarChange';
                    var csrf_token  = document.querySelector("meta[name='csrf-token']").getAttribute('content');
                    var rotateLeft  = document.querySelector('.tools > .left');
                    var rotateRight = document.querySelector('.tools > .right');
                    var submitCrop  = document.querySelector('.tools > .cropButton');
                    var gradRotate  = 0;
                    var imgEncoded  = null;

                    createCanvasElements();

                    var filesIMG = document.querySelector('.form_IMG_local > input[name="file"]');
                        filesIMG.addEventListener('change', function ()
                            {
                                var pattern = /\/jpg|\/png|\/jpeg|\/gif/;
                                var typeIMG = filesIMG.files[0].type;
                                var regX = pattern.test(typeIMG);

                                switch (true)
                                    {

                                        case filesIMG.files[0].size >= 20000000:
                                            errorHandler('.form_IMG_local', 'File is too Large!');
                                            break;

                                        case !regX:
                                            errorHandler('.form_IMG_local', 'Wrong Image Format!');
                                            break;

                                        case regX:

                                            var reader = new FileReader();

                                            reader.readAsDataURL(filesIMG.files[0]);

                                            reader.onload = function ()
                                                {
                                                    imgEncoded = reader.result;

                                                    canvasIMG(imgEncoded);
                                                };

                                            errorHandler('.form_IMG_local');

                                            break;
                                    }
                            }, false);

                    var urlIMG        = document.querySelector('.form_IMG_URL > input[name="url"]');
                    var urlIMGConfirm = document.querySelector('.form_IMG_URL > #submitAvatar');

                    urlIMGConfirm.addEventListener('click', function (e)
                        {
                            e.preventDefault();
                            e.stopPropagation();


                            var http = new XMLHttpRequest();
                            var url  = urlAvatar;
                            http.open('POST', url, true);

                            http.timeout = 4000;
                            http.ontimeout = function ()
                                {
                                    location.reload();
                                    alert('Your request exceeded the time limit for processing');
                                };

                            http.upload.onprogress = function (e)
                                {
                                    var canvas = document.querySelector('.avatarIMG_canvas');

                                    var ctx      = canvas.getContext("2d");
                                    canvas.width = window.innerWidth / 2.5;
                                    canvas.height= canvas.width / 1.7;

                                    ctx.font      = "3vmin Arial";
                                    ctx.fillStyle = "white";
                                    ctx.textAlign = "center";
                                    ctx.fillText("LOADING...", canvas.width / 2, canvas.height / 2);
                                };

                            http.onreadystatechange = function ()
                                {

                                    if (http.readyState == 4 && http.status == 200)
                                        {
                                            var response = http.responseText.replace(/<meta http-equiv='Refresh' content='0; url=.*'>/, "");

                                            imgEncoded   = "data:image/jpg;base64," + response;

                                            canvasIMG(imgEncoded);
                                        }
                                };

                            if (urlIMG.value != '')
                                {
                                    var pattern = /\.jpg|\.png|\.jpeg|\.gif/;
                                    var regX = pattern.test(urlIMG.value);

                                    var pattern2 = /data:image\/jpeg;base64,|data:image\/png;base64,|data:image\/gif;base64,|data:image\/jpg;base64,/;
                                    var regX2 = pattern2.test(urlIMG.value);

                                    switch (true)
                                        {
                                            case regX2:

                                                canvasIMG(urlIMG.value);
                                                break;

                                            case regX:

                                                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                                                http.setRequestHeader('X-CSRF-TOKEN', csrf_token );
                                                http.send('submitAvatar=' + urlIMG.value);
                                                break;

                                            default:

                                                var canvas = document.querySelector('.avatarIMG_canvas');
                                                var ctx = canvas.getContext("2d");

                                                canvas.width = window.innerWidth / 2.5;
                                                canvas.height = canvas.width / 1.7;

                                                ctx.font = "3vmin Arial";
                                                ctx.fillStyle = "white";
                                                ctx.textAlign = "center";
                                                ctx.fillText("ERROR", canvas.width / 2, canvas.height / 2);

                                        }
                                };
                        });

                    rotateLeft.addEventListener('click', function ()
                        {
                            gradRotate += 90;
                            canvasIMG(imgEncoded, gradRotate);
                        });
                    rotateRight.addEventListener('click', function ()
                        {
                            gradRotate -= 90;
                            canvasIMG(imgEncoded, gradRotate);
                        });
                    submitCrop.addEventListener('click', function ()
                        {
                            var croppedSave = document.querySelector('.cropped_Canvas');
                            var data        = croppedSave.toDataURL("image/jpeg").split(',')[1];

                            var http = new XMLHttpRequest();
                            var url  = urlAvatar;
                            http.open('POST', url, true);

                            http.onreadystatechange = function ()
                                {
                                    if (http.readyState == 4 && http.status == 200)
                                        {
                                            // var test = http.responseText;
                                            // console.log(test);
                                            location.reload();

                                        }
                                };

                            http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                            http.setRequestHeader('X-CSRF-TOKEN', csrf_token );
                            http.send('cropAvatar=' + data + '&sImgPath=<?= auth()->user()["avatar"]; ?>');
                        });

                    //__________________________________
                    //-----Open/Close cropper Window----
                    var avatarWrapper        = document.querySelector('.avatarWrapper');
                    var avatarWrapperWrapper = document.querySelector('.submit_profile');
                    var close_cropper        = document.querySelector('.close_cropper');

                    if (window.FileReader)
                        {
                            if (avatarWrapperWrapper)
                                {
                                    avatarWrapperWrapper.addEventListener('click', function ()
                                        {
                                            avatarWrapper.style.cssText = "display: block;";
                                        });
                                }
                            if (close_cropper)
                                {
                                    close_cropper.addEventListener('click', function ()
                                        {
                                            avatarWrapper.style.cssText = "display: none;";
                                        });
                                }
                        }
                    else
                        {
                            errorHandler('.avatarWrapperWrapper .profile_chang', 'Your browser can not perform this operation!', 'afterend');
                        }

                    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~

                }, false);

            var createCanvasElements = function createCanvasElements()
                {
                    var selecetdDiv          = document.querySelector('.avatarIMG_canvas_wrapper');
                    var Avatar_before_Insert = document.querySelector('.cropped_Canvas_wrapper');
                    var crop_Insert          = document.querySelector('.tools');

                    // ----Create elements----
                    var doc           = document.createElement('canvas');
                        doc.className = 'avatarIMG_canvas';

                    var miniCanvas           = document.createElement('canvas');
                        miniCanvas.className = 'cropped_Canvas';

                    var br = document.createElement('br');

                    selecetdDiv.appendChild(doc);
                    Avatar_before_Insert.insertBefore(miniCanvas, crop_Insert);
                    Avatar_before_Insert.insertBefore(br, crop_Insert);
                    //~~~~~~~~~~~~~~~~~~~~~~~~~
                };

            var canvasIMG            = function canvasIMG(imgEncodedSource)
                {
                    var grad = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;

                    var canvas = document.querySelector('.avatarIMG_canvas');

                    window.innerWidth >= 810 ? canvas.width = window.innerWidth / 2.5 : canvas.width = window.innerWidth / 1.2;
                    canvas.height = canvas.width / 1.7;
                    var ictx = canvas.getContext('2d');

                    var img = new Image();
                    img.src = imgEncodedSource;

                    var rColor     = "rgb(136, 221, 34)";
                    var innerBoxCr = "rgb(255, 0, 0)";

                    var rad = Math.PI / 180;

                    var mouse = {
                        x: undefined,
                        y: undefined,
                        mDown: false
                    };
                    var canvasCoord = canvas.getBoundingClientRect();

                    //Big rectangle static coordinates
                    var rBig = {
                        x: undefined,
                        y: undefined,
                        w: undefined,
                        h: undefined,
                        m_x: false,
                        m_y: false,
                        drag: false
                    };
                    // Big rectangle Boreder limits
                    var rBorders = {
                        min: 5,
                        max: canvas.width / 1.8
                    };

                    function dist(x2, x1) {
                        var y2 = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0;
                        var y1 = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 0;

                        return Math.sqrt((x2 - x1) * (x2 - x1) + (y2 - y1) * (y2 - y1));
                    }

                    var x = canvas.width / 2 - rBorders.max / 4;
                    var y = canvas.height / 2 - rBorders.max / 4;

                    var rect = new Rect(x, y, rBorders.max / 2, rBorders.max / 2, img, rColor, innerBoxCr);

                    function Rect(x, y, w, h, img)
                        {
                            var rColor     = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : 0;
                            var innerBoxCr = arguments.length > 6 && arguments[6] !== undefined ? arguments[6] : 0;


                            this.x = x;
                            this.y = y;
                            this.w = w;
                            this.h = h;
                            this.img = img;
                            this.boxSize = 10;
                            this.rColor  = rColor;
                            this.boxInrColor = innerBoxCr;

                            this.old_X1;
                            this.old_Y1;

                            this.drawRect = function ()
                                {

                                    ictx.beginPath();
                                    ictx.rect(this.x, this.y, this.w, this.h);
                                    ictx.lineWidth = 1;
                                    ictx.strokeStyle = this.rColor;
                                    ictx.stroke();
                                    // ictx.globalCompositeOperation = "xor";


                                    // ---Outer shadow Rectangle-----
                                    ictx.lineWidth = canvas.width;
                                    var innerLine = ictx.lineWidth / 2;
                                    ictx.strokeStyle = "rgba(68, 68, 68, .4745098039215686)";

                                    ictx.strokeRect(this.x - innerLine, this.y - innerLine, this.w + ictx.lineWidth, this.h + ictx.lineWidth);
                                };
                            //--------Drag Boxes---------
                            this.lTopBox = function ()
                                {
                                    this.lTop = {
                                        x: this.x - this.boxSize,
                                        y: this.y - this.boxSize
                                    };

                                    ictx.strokeStyle = this.rColor;
                                    ictx.lineWidth = 2;
                                    ictx.strokeRect(this.lTop.x, this.lTop.y, this.boxSize, this.boxSize);

                                    // ----Rect inside the Box----
                                    ictx.fillStyle = this.boxInrColor;
                                    var innerRline = ictx.lineWidth + 1;

                                    ictx.fillRect(this.lTop.x + innerRline, this.lTop.y + innerRline, this.boxSize - innerRline * 2, this.boxSize - innerRline * 2);

                                    return this.lTop;
                                };
                            this.rTopBox = function ()
                                {
                                    this.rTop = {
                                        x: this.x + this.w,
                                        y: this.y - this.boxSize
                                    };

                                    ictx.strokeRect(this.rTop.x, this.rTop.y, this.boxSize, this.boxSize);
                                    ictx.lineWidth = 2;
                                    ictx.strokeStyle = this.rColor;

                                    // ----Rect inside the Box----
                                    ictx.fillStyle = this.boxInrColor;
                                    var innerRline = ictx.lineWidth + 1;

                                    ictx.fillRect(this.rTop.x + innerRline, this.rTop.y + innerRline, this.boxSize - innerRline * 2, this.boxSize - innerRline * 2);

                                    return this.rTop;
                                };
                            this.lBottomBox = function () {

                                this.lBottom = {
                                    x: this.x - this.boxSize,
                                    y: this.y + this.h
                                };

                                ictx.rect(this.lBottom.x, this.lBottom.y, this.boxSize, this.boxSize);
                                ictx.lineWidth = 2;
                                ictx.strokeStyle = this.rColor;
                                ictx.stroke();

                                // ----Rect inside the Box----
                                ictx.fillStyle = this.boxInrColor;
                                var innerRline = ictx.lineWidth + 1;
                                ictx.fillRect(this.lBottom.x + innerRline, this.lBottom.y + innerRline, this.boxSize - innerRline * 2, this.boxSize - innerRline * 2);

                                return this.lBottom;
                            };
                            this.rBottomBox = function ()
                                {
                                    this.rBottom = {
                                        x: this.x + this.w,
                                        y: this.y + this.h,
                                        boxS: function boxS(bSize)
                                            {
                                                var boxSizeP = undefined;

                                                window.innerWidth < 810 ? boxSizeP = 17 : boxSizeP = bSize;

                                                return boxSizeP;
                                            }
                                    };

                                    ictx.strokeRect(this.rBottom.x, this.rBottom.y, rect.rBottom.boxS(this.boxSize), rect.rBottom.boxS(this.boxSize));
                                    ictx.lineWidth   = 2;
                                    ictx.strokeStyle = this.rColor;

                                    // ----Rect inside the Box----
                                    ictx.fillStyle = this.boxInrColor;
                                    var innerRline = ictx.lineWidth + 1;
                                    ictx.fillRect(this.rBottom.x + innerRline, this.rBottom.y + innerRline, rect.rBottom.boxS(this.boxSize) - innerRline * 2, rect.rBottom.boxS(this.boxSize) - innerRline * 2);

                                    return this.rBottom;
                                };
                            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~

                            //----Rectangle Restriction Border Color-----
                            this.RestrColor = function (x, y, w, h, translateX, translateY)
                                {
                                    this.x1 = x;
                                    this.y1 = y;
                                    this.x2 = w;
                                    this.y2 = h;

                                    this.translateLineX = translateX;
                                    this.translateLineY = translateY;

                                    if (this.translateLineX != 0)
                                        {
                                            for (var i = 0; i <= this.translateLineX; i += this.translateLineX)
                                                {
                                                    ictx.beginPath();
                                                    ictx.strokeStyle = 'red';
                                                    ictx.moveTo(this.x1 + i, this.y1);
                                                    ictx.lineTo(this.x2 + i, this.y2);
                                                    ictx.stroke();
                                                }
                                        };
                                    if (this.translateLineY != 0)
                                        {
                                            for (var i = 0; i <= this.translateLineY; i += this.translateLineY)
                                                {
                                                    ictx.beginPath();
                                                    ictx.strokeStyle = 'red';
                                                    ictx.moveTo(this.x1, this.y1 + i);
                                                    ictx.lineTo(this.x2, this.y2 + i);
                                                    ictx.stroke();
                                                }
                                        };
                                };

                            //----Aspect Ratio And Rotation-----
                            this.aspect = function ()
                                {
                                    var ratio = Math.min(canvas.width / this.img.width, canvas.height / this.img.height);

                                    return { width: this.img.width * ratio, height: this.img.height * ratio };
                                };
                            this.imageUrl = function ()
                                {

                                    ictx.save();
                                    ictx.translate(canvas.width / 2, canvas.height / 2);
                                    ictx.rotate(grad * rad);

                                    ictx.drawImage(this.img, -(this.aspect().width / 2), -(this.aspect().height / 2), this.aspect().width, this.aspect().height);
                                    ictx.restore();
                                };
                        }

                    // ---Animate Obgects,Moving Objects----
                    function anim()
                        {

                            ictx.clearRect(0, 0, canvas.width, canvas.height);

                            rect.imageUrl();

                            rect.drawRect();

                            rect.lTopBox();
                            rect.rTopBox();
                            rect.lBottomBox();
                            rect.rBottomBox();

                            // ----Cropped Image Animate----
                            imgCropped();

                            //-----Rectangle Restriction Border Color-----
                            if (rect.w >= rBorders.max) rect.RestrColor(rect.x, rect.y, rect.x, rect.y + rect.h, rect.w, 0);
                            if (rect.h >= rBorders.max) rect.RestrColor(rect.x, rect.y, rect.x + rect.w, rect.y, 0, rect.h);

                            // ---- Rectangle ----
                            if (mouse.x > rect.x && mouse.x < rect.x + rect.w && mouse.y > rect.y && mouse.y < rect.y + rect.h && mouse.mDown === true && rBig.drag === true)
                                {

                                    //-----Drag Rectangle----
                                    rect.x = mouse.x - rBig.m_x;
                                    rect.y = mouse.y - rBig.m_y;

                                    //----Border Restrictions----
                                    switch (true)
                                        {
                                            case rect.x + rect.w >= canvas.width:

                                                rect.x = canvas.width - rect.w;

                                                rect.y <= 0 ? rect.y = 0 : rect.y; //Bag Fixing (fallin out of borders)
                                                rect.y >= canvas.height - rect.h ? rect.y = canvas.height - rect.h : rect.y; //Bag Fixing (fallin out of borders)
                                                break;

                                            case rect.x <= 0:

                                                rect.x = 0;

                                                rect.y <= 0 ? rect.y = 0 : rect.y; //Bag Fixing (fallin out of borders)
                                                rect.y >= canvas.height - rect.h ? rect.y = canvas.height - rect.h : rect.y; //Bag Fixing (fallin out of borders)
                                                break;

                                            case rect.y <= 0:

                                                rect.y = 0;
                                                break;

                                            case rect.y + rect.h >= canvas.height:

                                                rect.y = canvas.height - rect.h;
                                                break;

                                        }
                                }

                            // ------ Boxes ------
                            switch (true)
                                {
                                    // ---Left Top Box-----
                                    case mouse.x > rect.lTop.x && mouse.x < rect.lTop.x + rect.boxSize && mouse.y > rect.lTop.y && mouse.y < rect.lTop.y + rect.boxSize && mouse.mDown === true && rBig.drag === true:

                                        rect.x = mouse.x + rect.boxSize / 2;
                                        rect.y = mouse.y + rect.boxSize / 2;
                                        rect.w = rBig.w - rect.x;
                                        rect.h = rBig.h - rect.y;

                                        break;

                                    //----Left Bottom Box----
                                    case mouse.x > rect.lBottom.x && mouse.x < rect.lBottom.x + rect.boxSize && mouse.y > rect.lBottom.y && mouse.y < rect.lBottom.y + rect.boxSize && mouse.mDown === true && rBig.drag === true:

                                        rect.x = mouse.x + rect.boxSize / 2;
                                        rect.w = rBig.w - rect.x;
                                        rect.h = mouse.y - rect.boxSize / 2 - rect.y;

                                        break;

                                    //----Right Top Box------
                                    case mouse.x > rect.rTop.x && mouse.x < rect.rTop.x + rect.boxSize && mouse.y > rect.rTop.y && mouse.y < rect.rTop.y + rect.boxSize && mouse.mDown === true && rBig.drag === true:

                                        rect.y = mouse.y + rect.boxSize / 2;
                                        rect.w = mouse.x - rect.boxSize / 2 - rect.x;
                                        rect.h = rBig.h - rect.y;

                                        break;

                                    //----Right Bottom Box-----
                                    case mouse.x > rect.rBottom.x && mouse.x < rect.rBottom.x + rect.rBottom.boxS(rect.boxSize) && mouse.y > rect.rBottom.y && mouse.y < rect.rBottom.y + rect.rBottom.boxS(rect.boxSize) && mouse.mDown === true && rBig.drag === true:

                                        rect.w = mouse.x - rect.rBottom.boxS(rect.boxSize) / 2 - rect.x;
                                        rect.h = mouse.y - rect.rBottom.boxS(rect.boxSize) / 2 - rect.y;

                                        break;

                                }

                            //----Rect Limits and Borders----
                            switch (true)
                                {
                                    // Left Boxes Horizontal borders
                                    case rect.w <= rBorders.min && dist(mouse.x, rect.x - rect.boxSize / 2) < rect.boxSize / 2 && mouse.mDown:

                                        rect.w = rect.w + 10;
                                        rect.x = rect.x - 10;
                                        break;

                                    //Right boxes horizontal borders
                                    case rect.w * 2 <= rBorders.min * 2:

                                        rect.w = rect.w + 10;
                                        break;

                                    //Top Boxes vertical borders
                                    case rect.h <= rBorders.min && dist(mouse.y, rect.y - rect.boxSize / 2) < rect.boxSize / 2 && mouse.mDown:

                                        rect.h = rect.h + 10;
                                        rect.y = rect.y - 10;
                                        break;

                                    //Bottom boxes vertical borders
                                    case rect.h * 2 <= rBorders.min * 2:

                                        rect.h = rect.h + 10;
                                        break;

                                    // -----------------------------------------
                                    //Right Strech border
                                    case rect.w >= rBorders.max && dist(mouse.x, rect.x + rect.w + rect.boxSize / 2) < rect.boxSize / 2 && mouse.mDown:

                                        rect.w = rBorders.max;

                                        if (rect.h >= rBorders.max && mouse.y <= rect.y) //Bag Fixing (fallin out of borders)
                                            {
                                                rect.y = rBig.h - rBorders.max;
                                                rect.h = rBorders.max;
                                            }
                                        if (rect.h >= rBorders.max && mouse.y >= rect.y + rect.h) //Bag Fixing (fallin out of borders)
                                            {
                                                rect.y = rBig.y;
                                                rect.h = rBorders.max;
                                            }

                                        break;

                                    //Left Strech border
                                    case rect.w >= rBorders.max && dist(mouse.x, rect.x - rect.boxSize / 2) < rect.boxSize / 2 && mouse.mDown:

                                        rect.x = rBig.w - rBorders.max;
                                        rect.w = rBorders.max;

                                        if (rect.h >= rBorders.max && mouse.y <= rect.y) //Bag Fixing (fallin out of borders)
                                            {
                                                rect.y = rBig.h - rBorders.max;
                                                rect.h = rBorders.max;
                                            }
                                        if (rect.h >= rBorders.max && mouse.y >= rect.y + rect.h) //Bag Fixing (fallin out of borders)
                                            {
                                                rect.y = rBig.y;
                                                rect.h = rBorders.max;
                                            }

                                        break;

                                    //Top Strech border
                                    case rect.h >= rBorders.max && dist(mouse.y, rect.y - rect.boxSize / 2) < rect.boxSize / 2 && mouse.mDown:

                                        rect.y = rBig.h - rBorders.max;
                                        rect.h = rBorders.max;
                                        break;

                                    //Bottom  Strech border
                                    case rect.h >= rBorders.max && dist(mouse.y, rect.y + rect.h + rect.boxSize / 2) < rect.boxSize / 2 && mouse.mDown:

                                        rect.h = rBorders.max;
                                        break;

                                }

                            if (window.requestAnimationFrame) requestAnimationFrame(anim);else setTimeout(anim, 1000 / 60);
                        }

                    anim();

                    // ---Relative Coordinates and Resize Window----
                    var rl_xy = {
                        x: rect.x,
                        y: rect.y,
                        w: rect.w,
                        h: rect.h,
                        cw: canvas.width,
                        cnh: canvas.height
                    };
                    window.addEventListener("resize", function ()
                        {
                            window.innerWidth >= 810 ? canvas.width = window.innerWidth / 2.5 : canvas.width = window.innerWidth / 1.2;
                            canvas.height = canvas.width / 1.7;
                            rBorders = {
                                        min: 5,
                                        max: canvas.width / 1.8
                                    };

                            rect.x = canvas.width * rl_xy.x / rl_xy.cw;
                            rect.y = canvas.height * rl_xy.y / rl_xy.cnh;

                            rect.w = canvas.width * rl_xy.w / rl_xy.cw;
                            rect.h = canvas.height * rl_xy.h / rl_xy.cnh;
                        });
                    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

                    canvas.addEventListener('mouseup', function ()
                        {

                            mouse.mDown = false;
                            rBig.drag = false;

                            // ---for Relative Coordinates----
                            rl_xy.x = rect.x;
                            rl_xy.y = rect.y;
                            rl_xy.w = rect.w;
                            rl_xy.h = rect.h;
                            rl_xy.cw = canvas.width;
                            rl_xy.cnh = canvas.height;
                        });
                    canvas.addEventListener('mouseout', function ()
                        {
                            mouse.mDown = false;
                        });
                    canvas.addEventListener("mousedown", mousedown);
                    canvas.addEventListener('mousemove', mousemove);

                    function mousedown()
                        {

                            mouse.mDown = true;
                            rBig.drag = false;

                            rBig.m_x = Math.abs(mouse.x - rect.x);
                            rBig.m_y = Math.abs(mouse.y - rect.y);

                            rBig.x = rect.x;
                            rBig.y = rect.y;
                            rBig.w = rect.x + rect.w;
                            rBig.h = rect.y + rect.h;

                            // event.preventDefault();
                            // event.stopPropagation();
                        }
                    function mousemove(e)
                        {

                            mouse.x = e.offsetX;
                            mouse.y = e.offsetY;

                            rBig.drag = true;
                            // ---Cursor Style Rectangle-----
                            // ---Cursor Style Boxes---------
                            switch (true)
                                {
                                    // ---Cursor Style Rectangle-----
                                    case mouse.x > rect.x && mouse.x < rect.x + rect.w && mouse.y > rect.y && mouse.y < rect.y + rect.h:

                                        canvas.style.cssText = 'cursor:all-scroll;';
                                        break;

                                    // ---Cursor Style Boxes-----
                                    case mouse.x > rect.lTop.x && mouse.x < rect.lTop.x + rect.boxSize && mouse.y > rect.lTop.y && mouse.y < rect.lTop.y + rect.boxSize:

                                        canvas.style.cssText = 'cursor:pointer;';
                                        break;

                                    case mouse.x > rect.lBottom.x && mouse.x < rect.lBottom.x + rect.boxSize && mouse.y > rect.lBottom.y && mouse.y < rect.lBottom.y + rect.boxSize:

                                        canvas.style.cssText = 'cursor:pointer;';
                                        break;

                                    case mouse.x > rect.rTop.x && mouse.x < rect.rTop.x + rect.boxSize && mouse.y > rect.rTop.y && mouse.y < rect.rTop.y + rect.boxSize:

                                        canvas.style.cssText = 'cursor:pointer;';
                                        break;

                                    case mouse.x > rect.rBottom.x && mouse.x < rect.rBottom.x + rect.rBottom.boxS(rect.boxSize) && mouse.y > rect.rBottom.y && mouse.y < rect.rBottom.y + rect.rBottom.boxS(rect.boxSize):

                                        canvas.style.cssText = 'cursor:pointer;';
                                        break;

                                    default:
                                        canvas.style.cssText = 'cursor:default';
                                }
                        }

                    // ---Touch Listener-----
                    canvas.addEventListener('touchend', function ()
                        {
                            rBig.drag = false;
                            mouse.mDown = false;

                            // ---for Relative Coordinates----
                            rl_xy.x = rect.x;
                            rl_xy.y = rect.y;
                            rl_xy.w = rect.w;
                            rl_xy.h = rect.h;
                            rl_xy.cw = canvas.width;
                            rl_xy.cnh = canvas.height;
                        });
                    canvas.addEventListener('touchstart', function (e)
                        {
                            mouse.mDown = true;
                            rBig.drag   = false;

                            canvasCoord = canvas.getBoundingClientRect();
                            mouse.x = e.touches[0].clientX - canvasCoord.x;
                            mouse.y = e.touches[0].clientY - canvasCoord.y;

                            rBig.m_x = Math.abs(mouse.x - rect.x);
                            rBig.m_y = Math.abs(mouse.y - rect.y);

                            rBig.x = rect.x;
                            rBig.y = rect.y;
                            rBig.w = rect.x + rect.w;
                            rBig.h = rect.y + rect.h;
                        });
                    canvas.addEventListener('touchmove', function (e)
                        {
                            canvasCoord = canvas.getBoundingClientRect();
                            mouse.x = e.touches[0].clientX - canvasCoord.x;
                            mouse.y = e.touches[0].clientY - canvasCoord.y;

                            rBig.drag = true;

                            e.preventDefault();
                            e.stopPropagation();
                        });

                    // ~~~~~~~~~~~~~~~~~~~~~~~~

                    function imgCropped()
                        {
                            var canvas2        = document.querySelector('.cropped_Canvas');
                                canvas2.width  = rect.w - 3;
                                canvas2.height = rect.h - 3;

                            var mctx = canvas2.getContext('2d');
                            // mctx.style.cssText = 'position : absolute';
                            mctx.clearRect(0, 0, canvas2.width, canvas2.height);

                            var image1 = ictx.getImageData(rect.x + 2, rect.y + 2, rect.w - 3, rect.h - 3);

                            mctx.putImageData(image1, 0, 0);
                        }
                };

            function errorHandler(selector)
                {
                    var errorText = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
                    var position  = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'beforeend';


                    var selectElement = document.querySelector(selector);

                    var queryString = selector + ' ' + '.error'; //Old error message
                    var error = document.querySelector(queryString);

                    error ? error.remove() : error;

                    if (errorText != '')
                        {
                            selectElement.insertAdjacentHTML(position, "<label class='error' style='color:red;'>" + errorText + "</label>");
                        }

                    // -------------Options--------------
                    // 'beforebegin': Before the element itself.
                    // 'afterbegin' : Just inside the element, before its first child.
                    // 'beforeend'  : Just inside the element, after its last child.
                    // 'afterend'   : After the element itself.
                }


        </script>

        <!---~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
                <!-- [ Analytics ] -->
            <? if ( auth()->user()["username"] && auth()->user()["userType"] == 'Admin') :?>

                <div class='dAnalitycs'>
                    <label class="lAnalitycs"><a href="analitycsApi.php"">Google Analitycs</a></label>
                </div>
            <? endif; ?>

        <!---~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        <!---~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
                <!-- [ Avatar change ] -->
        <!---~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

            <div class="avatarWrapper" style="display: none;">


                <a class="close_cropper" style="text-decoration:none;"> &#x2612; </a>

                <div class="both_canvas_wrapper">

                    <div class="avatarIMG_canvas_wrapper"></div>

                    <div class="cropped_Canvas_wrapper">

                        <div class="tools" style = ''>
                            <a class="right"  >&#8635;</a>
                            <a class="left"   >&#8634;</a>
                            <a class="cropButton" >crop &#10004;</a>
                        </div>

                    </div>
                </div>

                <div class="file_select_wrapper">

                    <input type="radio" name ='box' id="localImage" checked/>
                    <label class="localImage" for = "localImage"> Local image </label>

                    <input type="radio" name ='box' id="urlImage"/>
                    <label class="urlImage" for = "urlImage"  > URL image    </label>

                    <!-- File -->
                    <form action="" method="post" enctype="multipart/form-data" class="form_IMG_local">
                        @csrf
                        <input type="file" name="file" >

                    </form>

                    <!-- URL -->
                    <form action="" method="post"  class="form_IMG_URL" >
                        @csrf
                        <input type="text" name="url" placeholder="url">

                        <!-- <input type="submit" name="submitAvatar" id="submitAvatar"> -->
                        <button type="submit" name="submitAvatar" id="submitAvatar"> Submit </button>
                    </form>
                </div>
            </div>
            <!-- ----------------------------------------- -->

            <div class="avatarWrapperWrapper">

                <label class="profile_chang"> Add/change Avatar </label>
                <br>
                <h4 style = 'margin-left: 20px;'>Accepted Image File Formats: &nbsp jpg, png, gif.</h4>
                <br>
                <button type="submit"  class="submit_profile"> Submit </button>

            </div>



        <!---~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
                <!-- [ Profile change ] -->
        <!---~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <div class="profWrapper">

                <label class="profile_change">Profile</label>

                <form method="POST" action='profile_change' class="profile_forms">
                    @csrf
                    <br>
                    <?
                        //--Profile Succsessfully changed
                        !session('success') ? :print_r("<label class='success' style='color:green;'>".session('success')."</label><br>");
                    ?>

                    <label for="username">Username</label><br>
                    <input type="text" name='username' class="username" id="username" value="<?= auth()->user()["username"] ?>">
                    @error('username')
							<label class="error alert alert-danger">{{ $message }}</label>
							<br>
					@enderror
                    <br>

                    <label for="">E-mail</label><br>
                    <input type="text" name='email' class="email"  value="<?= auth()->user()["email"] ?>">
                    @error('email')
							<label class="error alert alert-danger">{{ $message }}</label>
							<br>
					@enderror
                    <br>

                    <label for="">First Name</label><br>
                    <input type="text" name='firstName' class="firstName"  value="<?= auth()->user()["firstName"]?>">
                    @error('firstName')
							<label class="error alert alert-danger">{{ $message }}</label>
							<br>
					@enderror
                    <br>
                    <label for="">Last Name</label><br>
                    <input type="text" name='lastName' class="lastName"  value="<?= auth()->user()["lastName"]?>">
                    @error('lastName')
							<label class="error alert alert-danger">{{ $message }}</label>
							<br>
					@enderror
                    <br>
                    <label for="">Phone Number</label><br>
                    <input type="text" name='phone' class="phone"  value="<?= auth()->user()["phone"]?>">
                    @error('phone')
							<label class="error alert alert-danger">{{ $message }}</label>
							<br>
					@enderror



                    <br><br>
                    <button type="submit" name="submit_profile_change" class="submit_profile">Change</button>
                </form>
            </div>



        <!---~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
                <!-- [ Password change ] -->
        <!---~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <div class="passChange">

                <label class="Pas_Change">Password</label><br>


                <form method="POST" action='profile_change' class="profile_forms">
                    @csrf
                    <br>
                    <label for="">New Password</label><br>
                    <input type="password"  name='password' class="password">
                    <br>
                    <label for="">Verify</label><br>
                    <input type="password"  name='password_confirmation' class="pasConfirm">
                    @error('password')
                        <label class="error alert alert-danger">{{ $message }}</label>
                        <br>
                    @enderror
                    <br>
                    <?
                        //--Password change success
                        !session('pas_success') ? :
                            print_r( "<label class='success' style='color:green;'>".session('pas_success')."</label>");
                    ?>
                    <br><br>
                    <button type="submit" name="submitPassChange" class="submit_profile">Change</button>
                </form>
            </div>



        <!---~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
                <!-- [ Delete Your Account ] -->
        <!---~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <div class="deleteAccount">

                    <label class="Pas_Change"><h4 style="color: red;">Delete Your Account</h2><br></label>


                    <form method="POST" action='profile_change' class="profile_forms">
                        @csrf
                        <br>
                        <label for="">Type in your email  to confirm account deletion.</label><br>
                        <input type="text" name='email_del' class="email" ">
                        @error('email_del')
                            <label class="error alert alert-danger">{{ $message }}</label>
                            <br>
                        @enderror
                        <br>


                        <label for="">Also, type in your password.</label><br>
                        <input type= "password"  name='password_del' class= "password">

                        @error('password_del')
                            <label class="error alert alert-danger">{{ $message }}</label>
                            <br>
                        @enderror
                        <br>
                        <?

                            //--Account deleted
                            !isset($success['delAcc']) ? :
                                print_r("<label class='success' style='color:green;'>".$success['delAcc']."</label>");
                        ?>

                        <br>
                        <button type="submit" name="submitDelete" class="submit_profile">Delete</button>
                    </form>
            </div>


    <?endif;?>
@endsection