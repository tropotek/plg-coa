-- --------------------------------------------
-- Author: Michael Mifsud <http://www.tropotek.com/>
-- --------------------------------------------
--
--
-- --------------------------------------------


alter table coa change profile_id course_id int unsigned default 0 not null;
drop index profile_id on coa;
create index course_id on coa (course_id);



