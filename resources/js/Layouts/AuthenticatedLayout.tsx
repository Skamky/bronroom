import ApplicationLogo from '@/Components/ApplicationLogo';
import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';
import Container from 'react-bootstrap/Container';
import {Link, usePage} from '@inertiajs/react';
import React, {PropsWithChildren, ReactNode} from 'react';
import {NavDropdown} from 'react-bootstrap';
import {Breadcrumbs} from "@gravity-ui//uikit";


export default function Authenticated({
                                          header,
                                          children,
                                      }: PropsWithChildren<{ header?: ReactNode }>) {
    const user = usePage().props.auth.user;


    return (
        <div className="min-h-screen">
            <Navbar expand="lg" className="border-bottom">
                <Container>
                    <Navbar.Brand as={Link} href="/">
                        <ApplicationLogo width="36" height="36"/>
                    </Navbar.Brand>
                    <Navbar.Toggle aria-controls="basic-navbar-nav"/>
                    <Navbar.Collapse id="basic-navbar-nav">
                        <Nav className="me-auto">
                            <Breadcrumbs showRoot>
                                <Breadcrumbs.Item href={'/'}>Главная</Breadcrumbs.Item>
                                <Breadcrumbs.Item href={'/offices/2'}>Офис Москвы</Breadcrumbs.Item>
                                <Breadcrumbs.Item href={'/offices/2/rooms/1'}>Выбранная комната</Breadcrumbs.Item>

                            </Breadcrumbs>
                        </Nav>
                        <Nav>
                            <NavDropdown
                                title={user.name}
                            >
                                {/*<Dropdown.Toggle variant="warning" id="dropdown-basic">*/}
                                {/*    {user.name}*/}
                                {/*</Dropdown.Toggle>*/}

                                {/*<Dropdown.Menu>*/}
                                <NavDropdown.Item as={Link} href={route('profile.edit')}>Profile</NavDropdown.Item>
                                <NavDropdown.Divider/>
                                <NavDropdown.Item as={Link} href={route('logout')} method="post">Log
                                    Out</NavDropdown.Item>
                                {/*</Dropdown.Menu>*/}
                            </NavDropdown>
                        </Nav>
                    </Navbar.Collapse>
                </Container>
            </Navbar>

            {header && (
                <header className="shadow">
                    <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {header}
                    </div>
                </header>
            )}

            <main>{children}</main>
        </div>
    );
}
