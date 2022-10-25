create table if not exists institution
(
    id       serial
    primary key,
    name     text not null,
    email    text not null,
    verified boolean
);

alter table institution
    owner to username;

create table if not exists course
(
    id             serial
    primary key,
    institution_id integer not null
    references institution,
    name           text    not null,
    description    text    not null,
    abbreviation   text,
    color          text,
    enforce_id     integer
);

alter table course
    owner to username;

create table if not exists course_folder
(
    id        serial
    primary key,
    course_id integer not null
    references course,
    name      text    not null
);

alter table course_folder
    owner to username;

create table if not exists location
(
    id        serial
    primary key,
    course_id integer not null
    references course,
    folder_id integer
    references course_folder
);

alter table location
    owner to username;

create table if not exists "user"
(
    id       serial
    primary key,
    email    text not null
    unique,
    username text not null
    unique,
    password text not null
);

alter table "user"
    owner to username;

create table if not exists admin
(
    user_id integer not null
    primary key
    references "user"
);

alter table admin
    owner to username;

create table if not exists course_member
(
    course_id integer not null
    references course,
    user_id   integer not null
    references "user",
    admin     boolean,
    primary key (course_id, user_id)
    );

alter table course_member
    owner to username;

create table if not exists institution_member
(
    institution_id integer not null
    references institution,
    user_id        integer not null
    references "user",
    admin          boolean,
    primary key (institution_id, user_id)
    );

alter table institution_member
    owner to username;

create table if not exists upload
(
    id        serial
    primary key,
    user_id   integer not null
    references "user",
    file_name text    not null,
    file_ext  text    not null,
    options   text
);

alter table upload
    owner to username;

create table if not exists content
(
    id          serial
    primary key,
    upload_id   integer               not null
    references upload,
    location_id integer               not null
    references location,
    name        text                  not null,
    converted   boolean default false not null,
    available   boolean default false not null
);

alter table content
    owner to username;

create table if not exists download
(
    content_id integer not null
    references content,
    time       integer not null,
    primary key (content_id, time)
    );

alter table download
    owner to username;