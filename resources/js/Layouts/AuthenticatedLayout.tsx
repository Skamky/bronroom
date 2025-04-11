import ApplicationLogo from '@/Components/ApplicationLogo';
import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';
import Container from 'react-bootstrap/Container';
import {Link, router, usePage} from '@inertiajs/react';
import React, {PropsWithChildren, ReactNode, useEffect} from 'react';
import {NavDropdown} from 'react-bootstrap';
import {Box, Breadcrumbs, Button, Icon} from "@gravity-ui/uikit";
import {useToaster} from "@gravity-ui/uikit";
import {Moon, Sun} from '@gravity-ui/icons';
import {useThemeContext} from '@/Components/Wrapper';

export default function Authenticated({
                                          header,
                                          children,
                                      }: PropsWithChildren<{ header?: ReactNode }>) {
    const {theme, setTheme} = useThemeContext();
    const toast = usePage().props.toast;
    const {add} = useToaster();

    useEffect(() => {
        if (toast) {
            add(toast);
        }
    }, [toast]);

    const user = usePage().props.auth.user;
    const BreadcrumbsData = usePage().props.breadcrumbs;

    const toggleTheme = () => {
        setTheme(theme === 'light' ? 'dark' : 'light');
    };

    return (
        <div className="min-h-screen">
            <Navbar expand="lg" className="border-bottom">
                <Container>
                    <Navbar.Brand as={Link} href="/">
                        <ApplicationLogo width="36" height="36"/>
                    </Navbar.Brand>
                    <Navbar.Toggle aria-controls="basic-navbar-nav"/>
                    <Navbar.Collapse id="basic-navbar-nav">
                        <Nav className="d-flex w-100">
                            <Box className="w-100">
                                <Breadcrumbs onAction={(key) => router.get(key as string)}>
                                    {BreadcrumbsData.map((item) =>
                                        <Breadcrumbs.Item key={item.url}>
                                            {item.text}
                                        </Breadcrumbs.Item>)}
                                </Breadcrumbs>
                            </Box>
                        </Nav>
                        <Nav>
                            <Button
                                view="flat"
                                onClick={toggleTheme}
                                className="me-2"
                            >
                                <Icon data={theme === 'light' ? Moon : Sun} size={16} />
                            </Button>
                            <NavDropdown
                                title={user.name}
                            >
                                <NavDropdown.Item as={Link} href={route('profile.edit')}>Profile</NavDropdown.Item>
                                <NavDropdown.Divider/>
                                <NavDropdown.Item as={Link} href={route('logout')} method="post">
                                    Log Out
                                </NavDropdown.Item>
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
