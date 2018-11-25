<!DOCTYPE html>
<html ng-app="webrenderApp" ng-controller="renderController">
<head>
    <title>WebRender - Take screenshots of any website</title>
    <meta name="description" content="Take screenshots of websites">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="css/loading-bar.css" rel="stylesheet">
</head>
<body ng-cloak>
<div class="container-fluid">
    <h1 class="page-header" onclick="window.location='https://webrender.co'">WebRender
        <small>Take screenshots of any website</small>
    </h1>

    <div id="alerts">
        <script type="text/ng-template" id="alert.html">
            <div ng-transclude></div>
        </script>
        <div uib-alert ng-repeat="alert in alerts" ng-class="'alert-' + (alert.type || 'info')" close="closeAlert($index)" dismiss-on-timeout="{{alert.timeout}}">{{alert.msg}}</div>
    </div>

    <div ng-hide="renderFinished" ng-init="initRenderOptions()" class="well">
        <div>
            <h2>About</h2>

            This website can be used to take screenshots of any page you specify.<br/>
            Images are currently only stored for one hour, so please don't use direct links to this website.<br/>
            <br/>
            There's also an <a href="#api">API</a>!
            <br/>
            <br/>
            This is free-to-use, please don't abuse it :-)
            <hr/>
        </div>

        <div>
            <h2>Render</h2>

            <form>
                <div class="form-group">
                    <label for="renderUrl">Website URL</label>
                    <input class="form-control" type="url" id="renderUrl" ng-model="renderUrl" ng-disabled="renderStarted">
                </div>
<!--                <br/>-->
<!--                <h3>Options</h3>-->

<!--                <label for="option-renderFormat">Format</label><select class="form-control" id="option-renderFormat" ng-model="renderFormat" ng-disabled="renderStarted">-->
<!--                    <option value="svg">SVG</option>-->
<!--                    <option value="png">PNG</option>-->
<!--                    <option value="jpeg">JPEG</option>-->
<!--                </select>-->
<!--                <br/>-->

<!--                <div ng-repeat="option in renderOptions">-->
<!--                    <label for="optionValue-{{option.key}}">{{option.key}}</label>-->
<!--                    <div ng-show="option.type == 'boolean'">-->
<!--                        <!---->
<!--                        <select class="form-control" id="optionValue-{{option.key}}" ng-model="option.value" ng-disabled="renderStarted">-->
<!--                            <option value="true">true</option>-->
<!--                            <option value="false">false</option>-->
<!--                        </select>-->
<!--                        -->
<!--                        <input type="checkbox" id="optionValue-{{option.key}}" ng-model="option.value" ng-disabled="renderStarted">-->
<!--                    </div>-->
<!--                    <div ng-show="option.type != 'boolean'">-->
<!--                        <input class="form-control" type="{{option.type}}" id="optionValue-{{option.key}}" ng-model="option.value" ng-disabled="renderStarted">-->
<!--                    </div>-->
<!--                </div>-->

                <br/>
                <button class="btn btn-success" type="submit" ng-disabled="!renderUrl || renderStarted" ng-click="startRender()">Render</button>
            </form>
        </div>
    </div>
    <div ng-show="renderFinished">
        <div class="well">
            <h2>Rendered</h2>
            <div class="pull-right">
                <a href="#" ng-href="{{renderedImage}}" download="WebRender" target="_blank">Download Image</a> | <strong>Expiration:</strong> {{(renderedData.expiration*1000 | date : "medium")}}
                <br/>
                <a href="#" ng-click="showDirectLink()" ng-hide="renderDirectLink && renderDirectLinkVisible">Show direct link to render again</a>
                <input class="form-control" readonly ng-show="renderDirectLink && renderDirectLinkVisible" ng-model="renderDirectLink">
            </div>

            <img ng-src="{{renderedImage}}" src="" style="    max-width: 100%;">
        </div>
        <br/>
        <hr/>
        <div class="well well-sm">
            <h3>Metadata</h3>
            <textarea style="min-width:100%;max-width:100%;min-height:500px;">{{renderedDataString}}</textarea>
        </div>
    </div>
    <div>
        <h3 id="api">API</h3>

        <pre><code>https://api.webrender.co/render?url=&lt;website url&gt;</code></pre>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js" integrity="sha384-V6/dyDFv85/V/Ktq3ez5B80/c9ZY7jV9c/319rqwNOz3h9CIPdd2Eve0UQBYMMr/" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular-route.js" integrity="sha384-k+Qp/8rZxoiiYGVjOBiZwkEp5yv6clgl2EmwNaE1oUMlfmEYgCWazxf4CyxfZiWG" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.4.0/angular-cookies.min.js" integrity="sha384-4Rqc4WCYcTgQGo7N/eJANj8VFLYiS0J/XRW6ThAPed/9YJmlNO7iD+ZdbeKi1Eqx" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/2.1.1/ui-bootstrap.min.js" integrity="sha384-wdLJc2YyjuluHF3HVyjY3+6XbqHKHmlryPgblRIwxza9hLmxD9YJtWs16lUOIJB0" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/2.1.1/ui-bootstrap-tpls.min.js" integrity="sha384-WtvotrktirgylB2PB0Rmy8GyjqcT3ho9v1cyvjNZYLj8KZC1bsjfE8S4pniiFqyZ" crossorigin="anonymous"></script>
<script src="https://anglibs.github.io/angular-location-update/angular-location-update.min.js" integrity="sha384-jwZhTNLxbFFhAc5v2tYH1/sMFcN13k0/TzlpdIqnf2aAxpzbRJyn1yqt+FiDbJ5C" crossorigin="anonymous"></script>

<script src="js/loading-bar.js"></script>

<?php
echo '<script src="js/script.js?' . rand() . '"></script>';
?>
</body>
</html>