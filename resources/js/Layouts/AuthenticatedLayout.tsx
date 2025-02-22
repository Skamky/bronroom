import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from 'react-bootstrap/Dropdown';
import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';
import Container from 'react-bootstrap/Container';
import {Link, usePage} from '@inertiajs/react';
import {PropsWithChildren, ReactNode} from 'react';

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
                            <Nav.Link as={Link} href={route('dashboard')} active={route().current('dashboard')}>
                                Dashboard
                            </Nav.Link>
                        </Nav>
                        <Nav>
                            <Dropdown>
                                <Dropdown.Toggle variant="success" id="dropdown-basic">
                                    {user.name}
                                </Dropdown.Toggle>

                                <Dropdown.Menu>
                                    <Dropdown.Item as={Link} href={route('profile.edit')}>Profile</Dropdown.Item>
                                    <Dropdown.Item as={Link} href={route('logout')} method="post">Log
                                        Out</Dropdown.Item>
                                </Dropdown.Menu>
                            </Dropdown>
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
