angular.module('OpiferRulesEngine', [])

    .factory('RuleService', ['$resource', function($resource) {
        return $resource('/app_dev.php/api/rules/:provider', {
            provider: "@provider"
        });
    }])

    /**
     * Rule Editor directive
     */
    .directive('ruleEditor', function() {
        tpl =
            '<input type="hidden" id="{{ formid }}" name="{{ name }}" value="{{ rule }}">' +
            '<div class="ruleeditor">' +
            '    <div class="rule" ng-if="!rule">' +
            '       <div class="layoutselect">' +
            '           <select class="form-control" ng-options="item.name group by item.group for item in catalog" ng-model="selected" ng-change="selectRule()">'+
            '               <option value="">Add rule…</option>'+
            '           </select> ' +
            '       </div>' +
            '    </div>' +
            '   <div ng-if="rule"><rule subject="rule" catalog="catalog"></rule></div>' +
            '</div>' +
            //'<div class="row"><div class="col-xs-12"><pre>{{rule | json: object }}</pre></div></div>' +
            '';

        return {
            restrict: 'E',
            transclude: true,
            scope: {
                name: '@',
                value: '@',
                formid: '@',
                provider: '@',
                context: '@',
                modelattribute: '@'
            },
            template: tpl,
            controller: function($scope, $http, $attrs, RuleService) {
                if ($scope.value.length <= 2 || typeof $scope.value === "undefined" || $scope.value === null) {
                   $scope.rule = null;
                } else {
                    var json = JSON.parse($scope.value);
                    $scope.rule = angular.fromJson(json);
                }
                $scope.selected = null;

                $scope.catalog = RuleService.query({
                    provider: $scope.provider,
                    context: $scope.context
                });

                // Removes a rule
                $scope.removeRule = function(rule) {
                    $scope.rule = null;
                };

                $scope.selectRule = function() {
                    $scope.rule = angular.copy(this.selected);
                };
            },
            link: function(scope, element, attrs) {
                
                if (angular.isDefined(scope.modelattribute) && scope.modelattribute != '') {
                    var attr = scope.modelattribute.replace('subject.parameters[\'', '').replace('\']', '');

                    // @todo Find a way to avoid those $parent's
                    if (scope.$parent.$parent.$parent.$parent.subject.parameters[attr]) {
                        scope.rule = scope.$parent.$parent.$parent.$parent.subject.parameters[attr];
                    }

                    // Watch for the rule to change, so we can add it to the transcluded rule variable
                    scope.$watch('rule', function(newValue, oldValue) {
                        if (newValue) {
                            // @todo Find a way to avoid those $parent's
                            scope.$parent.$parent.$parent.$parent.subject.parameters[attr] = newValue;
                        }
                    }, true);
                }
            }
        };
    })

    /**
     * Rule directive
     */
    .directive('rule', ['$compile', function($compile) {
        var tpl =
            '<div class="rule">' +
            '    <div class="cell">' +
            '        <label class="control-label">{{ subject.name }}</label>' +
            '    </div>' +
            '    <div class="values">' +
            '        <div ng-include = "getTemplate()"></div>' +
            '    </div>' +
            '    <div class="controls">' +
            '         <a class="fa fa-remove danger" ng-click="remove()"></a> ' +
            '    </div>' +
            '</div>' +
            '<div class="children" ng-if="subject.hasOwnProperty(\'children\')">' +
            '   <div ng-repeat="child in subject.children track by $index"><rule subject="child" catalog="catalog"></rule></div>' +
            '   <div class="rule">' +
            '       <div class="layoutselect">' +
            '           <select class="form-control" ng-options="item.name group by item.group for item in catalog" ng-model="newrule" ng-change="addRule()"><option value="">Add rule…</option></select> ' +
            '       </div>' +
            '   </div>' +
            '</div>'
        ;

        return {
            restrict: 'E',
            terminal: true,
            scope: {
                subject: '=',
                catalog: '='
            },
            template: tpl,
            link: function(scope, element, attrs, controller) {
                $compile(element.contents())(scope.$new());

                scope.newrule = null;
                scope.remove = function() {
                    scope.$parent.removeRule(scope.subject);
                };
                scope.removeRule = function(rule) {
                    scope.subject.children.splice( scope.subject.children.indexOf(rule), 1 );
                };
                scope.selectRule = function() {
                    scope.subject = angular.copy(this.newrule);
                };
                scope.addRule = function() {
                    scope.subject.children.push(angular.copy(this.newrule));
                    this.newrule = null;
                };
                scope.getTemplate = function() {
                    return  '/bundles/opiferrulesengine/app/ruleeditor/partials/'+ scope.subject._class +'.html';
                };
                scope.pickObject = function (objectId) {
                    if (angular.isUndefined(scope.subject.right.value)) {
                        scope.subject.right.value = [];
                    }
                    scope.subject.right.value.push(objectId);
                };
                scope.unpickObject = function (objectId) {
                    scope.subject.right.value.splice( scope.subject.right.value.indexOf(objectId), 1 );
                };
                scope.selectObject = function($event, id) {
                  var checkbox = $event.target;
                  (checkbox.checked) ? scope.pickObject(id) : scope.unpickObject(id);
                };
                scope.isObjectSelected = function(id) {
                  if (angular.isUndefined(scope.subject.right.value)) {
                      return false;
                  }
                  return scope.subject.right.value.indexOf(id) >= 0;
                };
                
                // Get options from catalog rather than subject to ensure an
                // up-to-date option list.
                scope.getOptions = function() {
                    for (index = 0; index < scope.catalog.length; ++index) {
                        if (scope.catalog[index].name == scope.subject.name &&
                            scope.catalog[index]._class == scope.subject._class) {
                            
                            return scope.catalog[index].options;
                        }
                    }
                    return [];
                };
            }
        };
    }])
//
//    .directive('rulevalues', ['$compile', function($compile) {
//        return {
//            restrict: 'E',
//            transclude: true,
//            scope: {},
//            templateUrl: function(tElement, tAttrs) {
//                console.log(tElement, tAttrs);
//                return '/bundles/opiferrulesengine/js/components/partials/ConditionSet.html';
//            },
//            link: function (scope, element) {
//            }
//        };
//    }])
;
