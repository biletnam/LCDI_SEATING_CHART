# library imports
from os import path, listdir
from werkzeug import Response
from werkzeug.utils import redirect
# local imports
from baseController import BaseController, lookup, jsonify
from ..model import Task, UserTaskRoles, UserRoles, TaskStatus, TaskHistory, ForemanOptions, TaskType
from ..utils.utils import multidict_to_dict, session, ROOT_DIR
from ..forms.forms import AssignInvestigatorForm, EditTaskUsersForm, EditTaskForm, AddTaskForm, RequesterAddTaskForm


class TaskController(BaseController):
    def view_all(self):
        self.check_permissions(self.current_user, 'Task', 'view-all')
        all_tasks = Task.get_active_tasks(user=self.current_user, case_perm_checker=self.check_permissions)
        user_primary_inv, user_secondary_inv = Task.get_tasks_assigned_to_user(user=self.current_user)
        return self.return_response('pages', 'view_tasks.html', all_tasks=all_tasks, user_primary_inv=user_primary_inv,
                                    user_secondary_inv=user_secondary_inv)

    def view_qas(self):
        self.check_permissions(self.current_user, 'Task', 'view-qas')
        completed = multidict_to_dict(self.request.args)
        if 'completed' in completed and completed['completed'] == "True":
            user_primary_qa, user_secondary_qa = Task.get_tasks_requiring_QA_by_user(user=self.current_user,
                                                                                     all=True)
            completed = True
        else:
            user_primary_qa, user_secondary_qa = Task.get_tasks_requiring_QA_by_user(user=self.current_user)
            completed = False
        qa_tasks = Task.get_active_QAs(user=self.current_user, case_perm_checker=self.check_permissions)
        return self.return_response('pages', 'view_qas.html', qa_tasks=qa_tasks, user_primary_qa=user_primary_qa,
                                    user_secondary_qa=user_secondary_qa, completed=completed)

    def view(self, case_id, task_id):
        task = self._validate_task(case_id, task_id)
        if task is not None:
            self.check_permissions(self.current_user, task, 'view')
            return self.return_response('pages', 'view_task.html', task=task)
        else:
            return self.return_404()

    def view_upload(self, case_id, task_id, upload_id):
        upload = self._validate_upload(case_id, task_id, upload_id)
        if upload is not None:
            self.check_permissions(self.current_user, upload.task, 'add_file')
            return self.return_response('pages', 'view_upload.html', upload=upload)
        else:
            return self.return_404()

    def delete_upload(self, case_id, task_id, upload_id):
        upload = self._validate_upload(case_id, task_id, upload_id)
        if upload is not None:
            self.check_permissions(self.current_user, upload.task, 'delete_file')

            closed = False
            confirm_close = multidict_to_dict(self.request.args)
            if 'confirm' in confirm_close and confirm_close['confirm'] == "true":
                upload.delete(self.current_user)
                closed = True

            return self.return_response('pages', 'delete_upload.html', upload=upload, closed=closed)
        else:
            return self.return_404()

    def change_statuses(self, case_id):
        case = self._validate_case(case_id)
        if case is not None:
            self.check_permissions(self.current_user, case, 'edit')
            args = multidict_to_dict(self.request.args)
            change = False
            if "status" in args and args["status"] in TaskStatus.preInvestigation:
                status = args["status"]
                all_tasks_created = len(set([task.status for task in case.tasks])) == 1 \
                                    and case.tasks[0].status == TaskStatus.CREATED
                if not all_tasks_created:
                    return self.return_404()
                if 'confirm' in args and args['confirm'] == "true":
                    for task in case.tasks:
                        task.set_status(status, self.current_user)
                        change = True
                return self.return_response('pages', 'confirm_task_statuses_change.html', case=case, change=change,
                                            status=status)
            else:
                return self.return_404(reason="The case or status change you are trying to make does not exist.")
        else:
            return self.return_404(reason="The case or status change you are trying to make")

    def change_status(self, case_id, task_id):
        task = self._validate_task(case_id, task_id)
        if task is not None:
            self.check_permissions(self.current_user, task, 'edit')
            args = multidict_to_dict(self.request.args)
            change = False
            if "status" in args and args["status"] in TaskStatus.all_statuses:
                status = args["status"]
                if 'confirm' in args and args['confirm'] == "true":
                    task.set_status(status, self.current_user)
                    change = True
                return self.return_response('pages', 'confirm_task_status_change.html', task=task, change=change,
                                            status=status)
            else:
                return self.return_404(reason="The case or status change you are trying to make does not exist.")
        else:
            return self.return_404(reason="The case or status change you are trying to make does not exist.")

    def add(self, case_id):
        case = self._validate_case(case_id)
        if case is not None:
            self.check_permissions(self.current_user, case, 'add-task')
            is_requester = self.current_user.is_requester()
            task_type_options = [(tt.replace(" ", "").lower(), tt) for tt in TaskType.get_task_types()]

            args = multidict_to_dict(self.request.args)
            if 'type' in args and args['type'] == "requester" and is_requester:
                if self.validate_form(RequesterAddTaskForm()):
                    task_name = ForemanOptions.get_next_task_name(case, self.form_result['task_type'])
                    new_task = Task(case, self.form_result['task_type'], task_name,
                                    self.current_user, self.form_result['background'])
                    session.add(new_task)
                    session.flush()
                    new_task.add_change(self.current_user)
                    session.flush()
                    return self.return_response('pages', 'task_added.html', task=new_task)
                else:
                    return self.return_response('pages', 'add_task.html', task_type_options=task_type_options,
                                                case=case, errors=self.form_error, is_requester=is_requester)
            elif self.validate_form(AddTaskForm()):
                new_task = Task(case, self.form_result['task_type'], self.form_result['task_name'], self.current_user,
                                self.form_result['background'], self.form_result['location'])
                session.add(new_task)
                session.flush()
                new_task.add_change(self.current_user)
                session.flush()

                if self.form_result['primary_investigator']:
                    self._create_new_user_role(UserTaskRoles.PRINCIPLE_INVESTIGATOR, new_task,
                                               self.form_result['primary_investigator'])
                if self.form_result['secondary_investigator']:
                    self._create_new_user_role(UserTaskRoles.SECONDARY_INVESTIGATOR, new_task,
                                               self.form_result['secondary_investigator'])
                if self.form_result['primary_qa']:
                    self._create_new_user_role(UserTaskRoles.PRINCIPLE_QA, new_task, self.form_result['primary_qa'])
                if self.form_result['secondary_qa']:
                    self._create_new_user_role(UserTaskRoles.SECONDARY_QA, new_task, self.form_result['secondary_qa'])

                session.commit()
                return redirect(
                    self.urls.build('case.view', {"case_id": case.case_name}))  # CaseController.view(case.case_name)
            else:
                investigators = [(user.id, user.fullname) for user in UserRoles.get_investigators()]
                qas = [(user.id, user.fullname) for user in UserRoles.get_qas()]
                return self.return_response('pages', 'add_task.html', investigators=investigators, qas=qas,
                                            task_type_options=task_type_options, case=case, errors=self.form_error,
                                            is_requester=is_requester)
        else:
            return self.return_404()

    def edit(self, task_id, case_id):
        task = self._validate_task(case_id, task_id)
        if task is not None:
            self.check_permissions(self.current_user, task, 'edit')

            task_type_options = [(tt.replace(" ", "").lower(), tt) for tt in TaskType.get_task_types()]
            investigators = [(user.id, user.fullname) for user in UserRoles.get_investigators()]
            qas = [(user.id, user.fullname) for user in UserRoles.get_qas()]
            status_options = [(status, status) for status in TaskStatus.all_statuses]
            form_type = multidict_to_dict(self.request.args)
            active_tab = 0

            if 'form' in form_type and form_type['form'] == "edit_task":
                if self.validate_form(EditTaskForm()):
                    if task.task_name != self.form_result['task_name'] or task.task_type != self.form_result[
                        'task_type'] or task.background != self.form_result['background'] or task.location != \
                            self.form_result['location']:
                        task.task_name = self.form_result['task_name']
                        task.task_type = self.form_result['task_type']
                        task.location = self.form_result['location']
                        task.background = self.form_result['background']
                        task.add_change(self.current_user)
                    if task.status != self.form_result['status']:
                        task.set_status(self.form_result['status'], self.current_user)
            elif 'form' in form_type and form_type['form'] == "edit_users":
                active_tab = 1
                if self.validate_form(EditTaskUsersForm()):
                    if task.principle_investigator != self.form_result['primary_investigator']:
                        self._create_new_user_role(UserTaskRoles.PRINCIPLE_INVESTIGATOR, task,
                                                   self.form_result['primary_investigator'])
                    if task.secondary_investigator != self.form_result['secondary_investigator']:
                        self._create_new_user_role(UserTaskRoles.SECONDARY_INVESTIGATOR, task,
                                                   self.form_result['secondary_investigator'])
                    if task.principle_QA != self.form_result['primary_qa']:
                        self._create_new_user_role(UserTaskRoles.PRINCIPLE_QA, task, self.form_result['primary_qa'])
                    if task.secondary_QA != self.form_result['secondary_qa']:
                        self._create_new_user_role(UserTaskRoles.SECONDARY_QA, task, self.form_result['secondary_qa'])

            task_history = self._get_tasks_history_changes(task)
            task_investigators_and_qa_history = self._get_investigators_and_qa_history_changes(task)
            principle_inv = task.principle_investigator.fullname if task.principle_investigator else "Please Select"
            secondary_inv = task.secondary_investigator.fullname if task.secondary_investigator else "Please Select"
            principle_qa = task.principle_QA.fullname if task.principle_QA else "Please Select"
            secondary_qa = task.secondary_QA.fullname if task.secondary_QA else "Please Select"
            return self.return_response('pages', 'edit_task.html', task=task, active_tab=active_tab,
                                        task_history=task_history,
                                        task_investigators_and_qa_history=task_investigators_and_qa_history,
                                        investigators=investigators, principle_inv=principle_inv, qas=qas,
                                        secondary_inv=secondary_inv, status_options=status_options,
                                        secondary_qa=secondary_qa, principle_qa=principle_qa,
                                        task_type_options=task_type_options, errors=self.form_error)
        else:
            return self.return_404()

    def close(self, task_id, case_id):
        task = self._validate_task(case_id, task_id)
        if task is not None:
            self.check_permissions(self.current_user, task, 'close')

            closed = False
            confirm_close = multidict_to_dict(self.request.args)
            if 'confirm' in confirm_close and confirm_close['confirm'] == "true":
                task.close_task(self.current_user)
                closed = True

            return self.return_response('pages', 'confirm_close_task.html', task=task, closed=closed)
        else:
            return self.return_404(reason="The task you are trying to close does not exist.")

    def assign_work(self, case_id, task_id):
        task = self._validate_task(case_id, task_id)
        if task is not None:
            self.check_permissions(self.current_user, task, 'assign-self')

            if task.principle_investigator is not None and task.secondary_investigator is not None:
                return self.return_404()

            first_assignment = multidict_to_dict(self.request.args)
            if "confirm" in first_assignment and first_assignment["confirm"] == "true":
                if "assign" in first_assignment and first_assignment['assign'] == "primary":
                    if first_assignment['assign'] == "primary" and task.principle_investigator is not None:
                        return self.return_404()
                    else:
                        task.assign_task(self.current_user, True)
                        return self.return_response('pages', 'assign_task.html', task=task, success=True,
                                                investigator=first_assignment['assign'])
                elif "assign" in first_assignment and first_assignment['assign'] == "secondary":
                    if first_assignment['assign'] == "secondary" and task.secondary_investigator is not None:
                        return self.return_404()
                    else:
                        task.assign_task(self.current_user, False)
                        return self.return_response('pages', 'assign_task.html', task=task, success=True,
                                                investigator=first_assignment['assign'])
                else:
                    return self.return_404()
            else:
                if "assign" in first_assignment and (first_assignment['assign'] == "primary"
                                                     or first_assignment['assign'] == "secondary"):
                    if first_assignment['assign'] == "primary" and task.principle_investigator is not None:
                        return self.return_404()
                    elif first_assignment['assign'] == "secondary" and task.secondary_investigator is not None:
                        return self.return_404()
                    else:
                        return self.return_response('pages', 'assign_task.html', task=task, success=False,
                                                    investigator=first_assignment['assign'])
                else:
                    return self.return_404()
        else:
            return self.return_404()

    def assign_work_manager(self, case_id, task_id):
        task = self._validate_task(case_id, task_id)
        if task is not None:
            self.check_permissions(self.current_user, task, 'assign-other')

            if task.principle_investigator is not None and task.secondary_investigator is not None:
                return self.return_404()

            if self.validate_form(AssignInvestigatorForm()):
                task.assign_task(self.form_result['investigator'], self.form_result['role'], self.current_user)
                if self.form_result['role']:
                    role_type = "Principle"
                else:
                    role_type = "Secondary"
                return self.return_response('pages', 'assign_task_manager.html', task=task, success=True,
                                            investigator=self.form_result['investigator'], role=role_type)
            else:
                roles = []

                if task.principle_investigator is None:
                    roles.append((UserTaskRoles.PRINCIPLE_INVESTIGATOR, UserTaskRoles.PRINCIPLE_INVESTIGATOR))
                if task.secondary_investigator is None:
                    roles.append((UserTaskRoles.SECONDARY_INVESTIGATOR, UserTaskRoles.SECONDARY_INVESTIGATOR))

                if task.principle_investigator is not None:
                    investigators = [(user.id, user.fullname) for user in UserRoles.get_investigators() if
                                     user.id != task.principle_investigator.id]
                elif task.secondary_investigator is not None:
                    investigators = [(user.id, user.fullname) for user in UserRoles.get_investigators() if
                                     user.id != task.secondary_investigator.id]
                else:
                    investigators = [(user.id, user.fullname) for user in UserRoles.get_investigators()]

                return self.return_response('pages', 'assign_task_manager.html', investigators=investigators,
                                            roles=roles, errors=self.form_error, task=task)
        else:
            return self.return_404()

    @staticmethod
    def _get_investigators_and_qa_history_changes(task):
        primary = UserTaskRoles.get_history(task, UserTaskRoles.PRINCIPLE_INVESTIGATOR)
        secondary = UserTaskRoles.get_history(task, UserTaskRoles.SECONDARY_INVESTIGATOR)
        primary_qa = UserTaskRoles.get_history(task, UserTaskRoles.PRINCIPLE_QA)
        secondary_qa = UserTaskRoles.get_history(task, UserTaskRoles.SECONDARY_QA)
        results = primary + secondary + primary_qa + secondary_qa
        results.sort(key=lambda d: d['date_time'])
        return results

    @staticmethod
    def _get_tasks_history_changes(task):
        history = TaskHistory.get_changes(task)
        status = TaskStatus.get_changes(task)
        results = history + status
        results.sort(key=lambda d: d['date_time'])
        return results

    def _create_new_user_role(self, role, task, form_result):
        try:
            user_role = UserTaskRoles.get_filter_by(task=task, role=role)[0]
            user_role.add_change(self.current_user, form_result)
            session.flush()
        except IndexError:
            if form_result is not True:
                new_role = UserTaskRoles(form_result, task, role)
                session.add(new_role)
                session.flush()
                new_role.add_change(self.current_user)